<?php


class OpenAPI2Slate {

	private $output_dir, $api_host, $index_file, $prepend_includes, $append_includes, $reference_objects;

	private $file_structure = [];

	public $openapi = null;

	public function __construct( $spec, $options = [] ) {

		// Validate OpenApi spec file
		if ( ! isset( $spec ) ) {
			trigger_error( 'No Spec File provided in construct', E_USER_ERROR );
		} else {

			// try to open the spec file
			$openapi = file_get_contents( $spec );
			if ( ! $openapi ) {
				trigger_error( 'Spec File provided not valid', E_USER_ERROR );
			}

			// validate file contents, proper JSON
			$openapi = json_decode( $openapi );
			if ( ! $openapi ) {
				trigger_error( 'Spec File provided not valid JSON', E_USER_ERROR );
			}

			$this->openapi = $openapi;
		}

		$this->reference_objects = [];

		//set api_host
		$this->api_host = $openapi->schemes[0] . "://" . $openapi->host . $openapi->basePath;

		//use provided output dir, or default to root
		$this->output_dir = $options['output'] ? $options['output'] : 'source/includes/';

		//use provided index file, or default to source/index.html.md
		$this->index_file = $options['index'] ? $options['index'] : 'source/index.html.md';

		//use prepend_includes or default to empty array
		$this->prepend_includes = $options['prepend_includes'] ? $options['prepend_includes'] : [];

		//use append_includes or default to empty array
		$this->append_includes = $options['append_includes'] ? $options['append_includes'] : [];

		$this->init_file_structure();

		$this->process_spec();

		$this->create_index();
	}

	private function init_file_structure() {

		//used to create the order of output, in tags order
		foreach ( $this->openapi->tags as $tag ) {
			$this->file_structure[ $tag->name ] = [];

			//create docs directory based on root
			$root = str_replace( " ", "_", strtolower( $tag->name ) );
			if ( ! file_exists( $this->output_dir . $root ) ) {
				mkdir( $this->output_dir . $root, 0777, true );
			}
		}

		//go over each path
		foreach ( $this->openapi->paths as $path => $details ) {

			$paths = explode( "/", $path );
			array_shift( $paths ); //remove blank leading value
			array_shift( $paths ); //remove root

			//go over each method for each path
			foreach ( $details as $method => $spec ) {
				$root = str_replace( " ", "_", strtolower( $spec->tags[0] ) );

				//create base file name
				$basename = implode( "_", $paths );
				$basename = str_replace( array( "{", "}" ), "", $basename );
				$basename = $method . "_" . $basename;

				//used for index file
				$spec->indexLocation = "  - " . $root . "/" . $basename;

				//create actual file
				$basename      = $basename . ".md.erb";
				$spec->writeTo = $this->output_dir . $root . "/" . $basename;
				if ( ! file_exists( $spec->writeTo ) ) {
					$fh = fopen( $spec->writeTo, "w" );
					fclose( $fh );
				}
				$this->file_structure[ $spec->tags[0] ][ $path ][ $method ] = $spec;
			}
		}
	}

	private function create_index() {

		//open index file
		$fh = fopen( $this->index_file, "r+" );

		// keep everything above includes:
		$length = 0;
		while ( $line = fgets( $fh ) ) {

			$length += strlen( $line );

			if ( strpos( $line, "includes:\r\n" ) !== false ) {
				break;
			}
		}
		ftruncate( $fh, $length );

		//prepend includes
		foreach ( $this->prepend_includes as $inc ) {
			fputs( $fh, "  - " . $inc . "\r\n" );
		}
		fputs( $fh, "\r\n" );

		// insert includes
		foreach ( $this->file_structure as $tag => $paths ) {

			//create an index for this tag
			$base = str_replace( " ", "_", strtolower( $tag ) ) . "/index";
			$index = fopen( $this->output_dir . $base . ".md.erb", "w" );
			fputs( $index, "# " . $tag . "\r\n\r\n" );


			//add reference object to index file
			$object_name = $tag;
			if(substr($object_name, -1) === 's'){
				$object_name = substr($object_name, 0,-1);
			}

			$tmp = $this->openapi->definitions->{str_replace(" ", "", $object_name)};
			$schema = $this->get_schema( $tmp );
			if($schema){
				fputs( $index, "## The " . $object_name . " object\r\n" );
				fputs( $index, $tmp->description . "\r\n\r\n" );
				$this->object_ref($index, $schema);
			}else{
                $tmp_name = $this->openapi->definitions->{substr($object_name, strpos($object_name, " ") + 1)};
                $tmp_schema = $this->get_schema( $tmp_name );
                if($tmp_schema){
                    fputs( $index, "## The " . $object_name . " object\r\n" );
                    fputs( $index, $tmp_name->description . "\r\n\r\n" );
                    $this->object_ref($index, $tmp_schema);
                }
            }

			fclose( $index );


			//add this tag index to includes
			fputs( $fh, "  - " . $base . "\r\n" );

			//add each spec to the includes file
			foreach ( $paths as $path => $methods ) {
				foreach ( $methods as $method => $spec ) {
					fputs( $fh, $spec->indexLocation . "\r\n" );
				}
			}

			fputs( $fh, "\r\n" );
		}

		//append includes
		foreach ( $this->append_includes as $inc ) {
			fputs( $fh, "  - " . $inc . "\r\n" );
		}

		//end index
		fputs( $fh, "\r\n---" );
		fclose( $fh );

	}

	private function process_spec() {

		//go over each spec
		foreach ( $this->file_structure as $tag => $paths ) {
			foreach ( $paths as $path => $methods ) {
				foreach ( $methods as $method => $spec ) {
					//open, truncate file
					$fh = fopen( $spec->writeTo, "w" );

					// h2 for content and label in left nav
					if ( $spec->summary ) {
						fputs( $fh, "## " . $spec->summary . "\r\n\r\n" );
					}

					if ( $spec->description ) {
						fputs( $fh, "" . $spec->description . "\r\n\r\n" );
					}

					//self explaining
					$this->write_definition( $fh, $method, $path );
					$this->write_example_request( $fh, $method, $path, $spec->parameters );
					$this->write_example_response( $fh, $spec->responses, $method, $path );
					$this->write_arguments( $fh, $method, $path, $spec->parameters );

					fclose( $fh );
				}
			}
		}
	}

	private function write_definition( $fh, $method, $path ) {

		fputs( $fh, "> Definition\r\n\r\n" );

		//curl definition
		fputs( $fh, "```shell\r\n" );
		fputs( $fh, strtoupper( $method ) . " " . $this->api_host . $path . "\r\n" );
		fputs( $fh, "```\r\n\r\n" );

		//TODO update node lib
		//javascript definition
//		fputs($fh,"```javascript\r\n");
//		fputs($fh,"```\r\n\r\n");

	}

	private function write_example_request( $fh, $method, $path, $params ) {

		fputs( $fh, "> Example Request\r\n\r\n" );

		//for example requests, determine require query and body params
		$query_string = [];
		$body_string  = '';
		if ( $params ) {
			$params = $this->get_parameters( $params );
			foreach ( $params as $param ) {
				if ( $param->required ) {

					if ( $param->in === "query" ) {
						$query_string[] = $param->name . "=" . $param->type;
					}

					if ( $param->in === "body" ) {
						$schema      = $this->get_schema( $param->schema );
						$body_string = $this->convert_schema_to_json( $schema, true );
					}
				}
			}
		}

		//determine if there was required query params
		if ( count( $query_string ) ) {
			$query_string = "?" . implode( "&", $query_string );
		} else {
			$query_string = "";
		}

		// curl example
		fputs( $fh, "```shell\r\n" );
		fputs( $fh, "$ curl " . $this->api_host . $path . $query_string );

		//body example for curl
		if ( strlen( $body_string ) ) {
			fputs( $fh, " \\\r\n   -d '" . $body_string ."'" );
		}

		//TODO what other methods need this? POST is implied with -d...
		if ( $method === 'delete' ) {
			fputs( $fh, " \\\r\n    -X DELETE" );
		}

		fputs( $fh, "\r\n```\r\n\r\n" );

		//TODO update node lib
		// javascript example
//		fputs($fh,"```javascript\r\n");
//		fputs($fh,"```\r\n\r\n");

	}

	private function write_example_response( $fh, $responses, $method, $path ) {

		fputs( $fh, "> Example Response\r\n\r\n" );

		//TODO update spec to include real examples
		$ex = '';
		foreach ( $responses as $status => $response ) {
			switch ( $status ) {
				case '200':
					$this->header_response( $fh, '200 OK' );
					break;
				case '201':
					$this->header_response( $fh, '201 Created' );
					break;
				case '204':
					$this->header_response( $fh, '204 No Content' );
					break;
				case '404':
				case '409':
					//$this->header_response( $fh, $status . ' ' . $response->description );
					break;
			}

			switch ( $status ) {
				case '200':
				case '201':
					if ( ! $response->schema ) {
						return;
					}

					$schema = $this->get_schema( $response->schema );
					$ex     = $this->convert_schema_to_json( $schema );

					if ( $method == 'get' && $path == '/accounts/{accountId}' ) {
						//var_dump($schema);
					}

					//json response
					fputs( $fh, "```json\r\n" );
					fputs( $fh, $ex . "\r\n" );
					fputs( $fh, "```\r\n\r\n" );
					break;
			}
		}
	}

	private function write_arguments( $fh, $method, $path, $params ) {

		//writes argument tables

		$p = [];

		//help order by path > query > body types
		$params = $this->get_parameters( $params );
		foreach ( $params as $param ) {
			$p[ $param->in ][] = $param;
		}

		if ( $p['path'] ) {
			$this->argument_helper( $fh, 'Path', $p['path'] );
		}

		if ( $p['query'] ) {
			$this->argument_helper( $fh, 'Query', $p['query'] );
		}

		if ( $p['body'] ) {
			$this->argument_helper_body( $fh, $p['body'] );
		}

	}

	private function argument_helper( $fh, $location, $params ) {

		//build arguments table

		fputs( $fh, $location . " Arguments | &nbsp;\r\n" );
		fputs( $fh, "--- | ---\r\n" );
		foreach ( $params as $param ) {

			//argument name
			$txt = $param->name;

			//required/optional
			$txt .= $param->required ? " **required**" : " *optional*";

			if($param->default) {
				$txt .= '<div class="default">' . $param->default . "</div>";
			}

			if($param->default === false) {
				$txt .= '<div class="default">false</div>';
			}

			if($param->minimum) {
				$txt .= '<div class="minimum">' . $param->minimum . "</div>";
			}

			if($param->maximum) {
				$txt .= '<div class="maximum">' . $param->maximum . "</div>";
			}

			if($param->maxLength) {
				$txt .= '<div class="maxLength">' . $param->maxLength . "</div>";
			}

			//description
			$txt .= " | " . $param->description;

			//display possible values if enum
			if($param->enum){
				$txt .= '<div class="enum">';
				foreach($param->enum as $i => $enum) {
					$txt .= '`'. $enum .'` ' . $param->{'x-enum-descriptions'}[$i] . "<br/>";
				}
				$txt .= '</div>';
			}

			fputs( $fh, $txt . "\r\n" );
		}
		fputs( $fh, "\r\n" );
	}

	private function argument_helper_body( $fh, $params ) {

		//body is JSON, needs unique function

		fputs( $fh, "Body Arguments | &nbsp;\r\n" );
		fputs( $fh, "--- | ---\r\n" );

		$_params = $this->get_schema( $params[0]->schema );

		foreach ( $_params as $name => $param ) {
			$this->body_params( $fh, $name, $param );
		}
		fputs( $fh, "\r\n" );
	}

	private function body_params( $fh, $name, $param, $parent = '' ) {

		//support nested objects

		if ( ! property_exists( $param, 'type' ) || is_object( $param->type ) ) {
			foreach ( $param as $child => $p ) {
				$this->body_params( $fh, $child, $p, $name . "." );
			}
		} else {
			$txt = $parent . $name;
			$txt .= $param->required ? " **required**" : " *optional*";

			if($param->default) {
				$txt .= '<div class="default">' . $param->default . "</div>";
			}

			if($param->default === false) {
				$txt .= '<div class="default">false</div>';
			}


			if($param->minimum) {
				$txt .= '<div class="minimum">' . $param->minimum . "</div>";
			}

			if($param->maximum) {
				$txt .= '<div class="maximum">' . $param->maximum . "</div>";
			}

			if($param->maxLength) {
				$txt .= '<div class="maxLength">' . $param->maxLength . "</div>";
			}

			$txt .= " | " . $param->description;

			//display possible values if enum
			if($param->enum){
				$txt .= '<div class="enum">';
				foreach($param->enum as $i => $enum) {
					$txt .= '`'. $enum .'` ' . $param->{'x-enum-descriptions'}[$i] . "<br/>";
				}
				$txt .= '</div>';
			}

			fputs( $fh, $txt . "\r\n" );
		}
	}

	private function header_response( $fh, $txt ) {

		//header example helper

		fputs( $fh, "```text\r\n" );
		fputs( $fh, $txt . "\r\n" );
		fputs( $fh, "```\r\n\r\n" );
	}

	private function get_parameters( $params ) {


		//get all parameters, in case they were $ref

		$tmp = [];

		foreach ( $params as $param ) {
			if ( property_exists( $param, '$ref' ) ) {
				$ref   = array_pop( explode( "/", $param->{'$ref'} ) );
				$ref   = $this->openapi->parameters->{$ref};
				$tmp[] = $ref;
			} else {
				$tmp[] = $param;
			}
		}

		return $tmp;
	}

	private function get_schema( $schema ) {

		//return complete schema object, needed because of $ref, allOf and other structures - normalize schema

		//fail safe, schema should be an object
		if ( ! is_object( $schema ) ) {
			return;
		}

		//if schema is $ref
		if ( property_exists( $schema, '$ref' ) ) {

			$tmp = array_pop( explode( "/", $schema->{'$ref'} ) );
			$tmp = $this->openapi->definitions->{$tmp};
			$tmp = $this->get_schema( $tmp );

			return $tmp;

		}

		//if schema is allOf
		if ( property_exists( $schema, 'allOf' ) ) {
			return $this->build_from_allOf( $schema->allOf );
		}

		//if schema is properties
		if ( property_exists( $schema, 'properties' ) ) {
			$tmp = new stdClass();

			foreach ( $schema->properties as $key => $property ) {

				if(is_array($schema->required) && in_array($key, $schema->required)){
					$property->required = true;
				}

				$tmp->{$key} = $this->get_schema( $property );
			}

			return $tmp;
		}

		//schema has been normalized
		return $schema;
	}

	private function build_from_allOf( $allOf ) {

		//help with allOf data structures

		$tmp = [];

		foreach ( $allOf as $a ) {
			//var_dump($a);
			$tmp[] = $this->get_schema( $a );
		}

		$_tmp = new stdClass();
		foreach ( $tmp as $obj ) {
			foreach ( $obj as $key => $value ) {
				$_tmp->{$key} = $value;
			}
		}

		return $_tmp;
	}

	private function convert_schema_to_json( $schema, $json_encode = true, $required_only = false ) {

		//used for request examples
		//take a schema and make it a json object

		if ( ! $schema ) {
			return;
		}

		if ( is_object( $schema ) && property_exists( $schema, 'type' ) && $schema->type === 'object' ) {
			return $this->convert_schema_to_json( $schema->properties, $json_encode, $required_only );
		}
		
		if ( is_object( $schema ) && property_exists( $schema, 'type' ) && $schema->type === 'string' ) {
            return "string";
        }

		$tmp = new stdClass();

		foreach ( $schema as $key => $value ) {

			if($required_only && !$value->required){
				continue;
			}

			if ( $value->type == "object" ) {
				$tmp->{$key} = $this->convert_schema_to_json( $value->properties, false, $required_only );
			} elseif ( $value->type == "array" ) {
				$sch         = $this->get_schema( $value->items );
				$tmp->{$key} = [ $this->convert_schema_to_json( $sch, false, $required_only ) ];
			} elseif ( is_object( $value->type ) ) {
				//this happens if a property is named 'type'
				$tmp->{$key} = $this->convert_schema_to_json( $value, false, $required_only );
			} elseif ( is_null( $value->type ) ) {
				$tmp->{$key} = $this->convert_schema_to_json( $value, false, $required_only );
			} else {
				$tmp->{$key} = $value->type;

				if($value->format){
					$tmp->{$key} = $value->type . " [" . $value->format . "]";
				}
			}
		}

		return $json_encode ? json_encode( $tmp, JSON_PRETTY_PRINT ) : $tmp;
	}

	private function object_ref( $fh, $params ) {

		$ex     = $this->convert_schema_to_json( $params );
		//json response
		fputs( $fh, "```json\r\n" );
		fputs( $fh, $ex . "\r\n" );
		fputs( $fh, "```\r\n\r\n" );


		fputs( $fh, "Property | Description\r\n" );
		fputs( $fh, "--- | ---\r\n" );
		foreach ( $params as $name => $param ) {
			$this->object_ref_params( $fh, $name, $param );
		}

		fputs( $fh, "\r\n" );
	}

	private function object_ref_params( $fh, $name, $param, $parent = '' ) {

		//support nested objects
		if ( ! property_exists( $param, 'type' ) || is_object( $param->type ) ) {
			foreach ( $param as $child => $p ) {
				$this->object_ref_params( $fh, $child, $p, $name . "." );
			}
		} else {
			$txt = $parent . $name;
			$txt .= " *" . $param->type . "*";
			$txt .= " | " . $param->description;

			//display possible values if enum
			if($param->enum){
				$txt .= '<div class="enum">';
				foreach($param->enum as $i => $enum) {
					$txt .= '`'. $enum .'` ' . $param->{'x-enum-descriptions'}[$i] . "<br/>";
				}
				$txt .= '</div>';
			}

			fputs( $fh, $txt . "\r\n" );
		}
	}
}

//options for openapi2slate
$options = array(
	'index'            => 'source/index.html.md',
	'prepend_includes' => array(
		'reference/index',
		'reference/authentication',
		'reference/errors',
		'reference/rate_limits',
		'reference/before_core'
	),
	'append_includes'  => array(
		'appendix/index',
		'appendix/master_account',
		'appendix/recurrence',
		'appendix/plans',
		'appendix/lists/index',
		'appendix/lists/state',
		'appendix/lists/country',
		'appendix/lists/timezone',
		'appendix/lists/callout_countries',
		'appendix/lists/tollfree_countries',
		'appendix/lists/premium_countries'
	),
);

// run parser
new OpenAPI2Slate( 'openapi.v2.json', $options );