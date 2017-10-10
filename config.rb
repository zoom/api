# Globals
config[:api_host]           = 'https://api.zoom.us/v2/'
config[:deveoper_portal]    = 'https://zoom.us/developer'
config[:ex_api_key]         = 'your_api_key'
config[:ex_api_secret]      = 'your_api_secret'
config[:ex_meeting_number]  = '123456789'
config[:ex_meeting_topic]   = 'Meeting Topic'
config[:ex_email]           = 'user@company.com'
config[:ex_password]        = 'P@55w0rd'
config[:ex_uuid]            = 'unique_id'
config[:ex_first_name]      = 'Zoomie'
config[:ex_last_name]       = 'Userton'
config[:ex_node_init]       = 'var Zoom = require("zoomus")({
    key : "your_api_key",
    secret : "your_api_secret"
});'
config[:ex_node_res]        = 'function(res){
    if(res.error){
      //handle error
    } else {
      console.log(res);
    }
}'

# Markdown
set :markdown_engine, :redcarpet
set :markdown,
    fenced_code_blocks: true,
    smartypants: true,
    disable_indented_code_blocks: true,
    prettify: true,
    tables: true,
    with_toc_data: true,
    no_intra_emphasis: true

# Assets
set :css_dir, 'stylesheets'
set :js_dir, 'javascripts'
set :images_dir, 'images'
set :fonts_dir, 'fonts'

# Activate the syntax highlighter
activate :syntax
ready do
  require './lib/multilang.rb'
end

activate :sprockets

activate :livereload

activate :autoprefixer do |config|
  config.browsers = ['last 2 version', 'Firefox ESR']
  config.cascade  = false
  config.inline   = true
end

# Github pages require relative links
activate :relative_assets
set :relative_links, true

# Build Configuration
configure :build do
  # If you're having trouble with Middleman hanging, commenting
  # out the following two lines has been known to help
  activate :minify_css
  activate :minify_javascript
  # activate :relative_assets
  # activate :asset_hash
  # activate :gzip
end

# Deploy Configuration
# If you want Middleman to listen on a different port, you can set that below
set :port, 4567