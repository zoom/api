# CHANGELOG

## For the latest Changelog in our APIs please see our developer site
- https://devdocs.zoom.us/guides/welcome/recent-updates

## 2017-05-20

### Added

- Meeting: "option_auto_record_type" parameter to create/update API.
- Meeting: "share_application" added to the response of the get API
- Meeting: "share_desktop" added to the response of the get API
- Meeting: "share_whiteboard" added to the response of the get API
- Meeting: "recording" added to the response of the get API
- Webinar: "option_auto_record_type" parameter to create/update API.
- Webinar: "registrant_id" to registration API.
- User: "status" parameter to list/get/getbyemail API.

### Notes

The following attributes would inherit Webinar Account level Settings when create Webinar via API:
- Close registration after event date
- Allow attendees to join from multiple devices
- Show social share buttons on registration page
- Add call log for telehealth API.

## 2017-04-08

### Added

- "v1/user/deactivate" API.
- "v1/h323/device/add" API.
- "v1/h323/device/update" API.
- "v1/h323/device/delete" API.
- "v1/h323/device/list" API.
- "enable_attention_tracking" and "enable_waiting_room" parameters to User Create/AutoCreate/CustCreate/Update API.
- "enable_use_pmi" parameter to User AutoCreate/CustCreate/Update API.
- access control to "v1/report/getaudioreport" API.

## 2017-02-25

### Added

- "v1/webinar/polls" API.
- "v1/webinar/questions" API.
- "password" and "option_practice_session" parameters to Webinar Create/Update API.
- "enable_phone_participants_password", "enable_auto_delete_cmr" and "auto_delete_cmr_days" parameters to User Create/AutoCreate/CustCreate/Update API
- "enable_only_host_download_cmr" and "enable_same_account_access_cmr" parameters to Account Create/Update API.

### Notes

- Support recurring Webinar feature in Webinar Create/Update/Get/List/Registration List/Delete/Registration API.

## 2017-01-15

### Added

- "v1/meeting/register" API.
- "v1/ma/account/plan/subscribe" API.
- "v1/ma/account/plan/add" API.
- "v1/ma/account/plan/update" API.
- "v1/ma/account/plan/get" API.
- "v1/ma/account/billing/update" API.

Add the following parameters to Account Create/Update API
- enable_share_rc
- share_rc
- enable_share_mc
- share_mc
- pay_mode
- collection_method

### Notes

- Support recurring meeting feature in Meeting Create/Update/Get/List/Delete API.
- Support CPU usage metrics in Qos API.

## 2016-12-03

## Added

-  "option_host_video" and "option_panelist_video" to Webinar create/update API.
-  "network_type" parameter to "v1/metrics/meetings" API.
-  "meeting_capacity" parameter to Account create/update API.
-  "option_use_pmi" parameter to Meeting create/update API.
-  "v1/webinar/rgistrants/list" API.
-  "v1/webinar/registrants/approve" API.
-  "v1/webinar/panelists" API.
-  "v1/webinar/registration/cancel" API.

Add the following parameters to user API - create/autocreate/autocreate2/custcreate
- disable_private_chat
- disable_group_hd
- enable_e2e_encryption
- enable_silent_mode
- disable_feedback
- disable_cancel_meeting_notification
- enable_breakout_room
- enable_polling
- enable_annotation
- enable_auto_saving_chats
- enable_co_host
- enable_enter_exit_chime
- option_enter_exit_chime_type
- enable_remote_support
- enable_file_transfer
- enable_virtual_background
- enable_closed_caption
- enable_far_end_camera_control
- enable_share_dual_camera

### Notes

- Support to update pending users in API
- Support to delete invite user in API
- Support to get pending user in API
