---
title: Zoom API

search: true

language_tabs:
  - shell: curl

toc_footers:
  - <a href='https://developer.zoom.us/'>Zoom Developers</a>
  - <a href='https://github.com/zoom/api/issues'>Report An Issue</a>
  - <br/><a href='https://zoom.github.io/api-v1'>Version 1 Docs</a>

includes:
  - reference/index
  - reference/authentication
  - reference/errors
  - reference/rate_limits
  - reference/before_core

  - accounts/index
  - accounts/get_
  - accounts/post_
  - accounts/get_accountId
  - accounts/delete_accountId
  - accounts/patch_accountId_options
  - accounts/get_accountId_settings
  - accounts/patch_accountId_settings

  - billing/index
  - billing/get_accountId_billing
  - billing/patch_accountId_billing
  - billing/get_accountId_plans
  - billing/post_accountId_plans
  - billing/put_accountId_plans_base
  - billing/post_accountId_plans_addons
  - billing/put_accountId_plans_addons

  - users/index
  - users/get_
  - users/post_
  - users/get_userId
  - users/patch_userId
  - users/delete_userId
  - users/get_userId_assistants
  - users/post_userId_assistants
  - users/delete_userId_assistants
  - users/delete_userId_assistants_assistantId
  - users/put_userId_picture
  - users/get_userId_settings
  - users/patch_userId_settings
  - users/put_userId_status
  - users/delete_userId_ssoToken
  - users/get_userId_token
  - users/get_userId_zpk

  - meetings/index
  - meetings/get_userId_meetings
  - meetings/post_userId_meetings
  - meetings/get_meetingId
  - meetings/patch_meetingId
  - meetings/delete_meetingId
  - meetings/put_meetingId_status
  - meetings/get_meetingId_registrants
  - meetings/post_meetingId_registrants
  - meetings/put_meetingId_registrants_registrantId_status

  - webinars/index
  - webinars/get_userId_webinars
  - webinars/post_userId_webinars
  - webinars/get_webinarId
  - webinars/patch_webinarId
  - webinars/delete_webinarId
  - webinars/put_webinarId_status
  - webinars/get_webinarId_panelists
  - webinars/post_webinarId_panelists
  - webinars/delete_webinarId_panelists
  - webinars/delete_webinarId_panelists_panelistId
  - webinars/get_webinarId_registrants
  - webinars/post_webinarId_registrants
  - webinars/put_webinarId_registrants_registrantId_status

  - groups/index
  - groups/get_
  - groups/post_
  - groups/get_groupId
  - groups/patch_groupId
  - groups/delete_groupId
  - groups/get_groupId_members
  - groups/post_groupId_members
  - groups/delete_groupId_members_memberId

  - im_groups/index
  - im_groups/get_groups
  - im_groups/post_groups
  - im_groups/get_groups_groupId
  - im_groups/patch_groups_groupId
  - im_groups/delete_groups_groupId
  - im_groups/get_groups_groupId_members
  - im_groups/post_groups_groupId_members
  - im_groups/delete_groups_groupId_members_userId

  - reports/index
  - reports/get_daily
  - reports/get_users
  - reports/get_users_userId_meetings
  - reports/get_meetings_meetingId_participants
  - reports/get_webinars_webinarId_participants
  - reports/get_webinars_webinarId_polls
  - reports/get_webinars_webinarId_qa
  - reports/get_telephone

  - dashboards/index
  - dashboards/get_meetings
  - dashboards/get_meetings_meetingId
  - dashboards/get_meetings_meetingId_participants
  - dashboards/get_meetings_meetingId_participants_participantId_qos
  - dashboards/get_meetings_meetingId_participants_qos
  - dashboards/get_meetings_meetingId_participants_participantId_sharing
  - dashboards/get_webinars
  - dashboards/get_webinars_webinarId
  - dashboards/get_webinars_webinarId_participants
  - dashboards/get_webinars_webinarId_participants_participantId_qos
  - dashboards/get_webinars_webinarId_participants_qos
  - dashboards/get_webinars_webinarId_participants_participantId_sharing
  - dashboards/get_zoomrooms
  - dashboards/get_zoomrooms_zoomroomId
  - dashboards/get_crc
  - dashboards/get_im

  - tsp/index
  - tsp/get_
  - tsp/get_userId_tsp
  - tsp/post_userId_tsp
  - tsp/get_userId_tsp_tspId
  - tsp/patch_userId_tsp_tspId
  - tsp/delete_userId_tsp_tspId

  - appendix/index
  - appendix/recurrence
  - appendix/plans
  - appendix/lists/index
  - appendix/lists/state
  - appendix/lists/country
  - appendix/lists/timezone
  - appendix/lists/tollfree_countries
  - appendix/lists/premium_countries

---