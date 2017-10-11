<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
    const TABLE_CALENDAR_EVENT_NAME = 'assol_calendar_event';

    const TABLE_CUSTOMER_NAME = 'assol_customer';
    const TABLE_CUSTOMER_HISTORY_NAME = 'assol_customer_history';
    const TABLE_CUSTOMER_ALBUM_NAME = 'assol_customer_album';
    const TABLE_CUSTOMER_ALBUM_2_IMAGE_NAME = 'assol_customer_album2image';
    const TABLE_CUSTOMER_AGREEMENT_NAME = 'assol_customer_agreement';
    const TABLE_CUSTOMER_PASSPORT_SCAN_NAME = 'assol_customer_passport_scan';
    const TABLE_CUSTOMER_LANGUAGE_NAME = 'assol_customer_language';
    const TABLE_CUSTOMER_CHILDREN_NAME = 'assol_customer_children';
    const TABLE_CUSTOMER_QUESTION_NAME = 'assol_customer_question';
    const TABLE_CUSTOMER_QUESTION_PHOTO_NAME = 'assol_customer_question_photo';
    const TABLE_CUSTOMER_QUESTION_TEMPLATE_NAME = 'assol_customer_question_template';
    const TABLE_CUSTOMER_QUESTION_ANSWER_NAME = 'assol_customer_question_answer';
    const TABLE_CUSTOMER_VIDEO_NAME = 'assol_customer_video';
    const TABLE_CUSTOMER_SITE_NAME = 'assol_customer_site';
    const TABLE_CUSTOMER_EMAIL_NAME = 'assol_customer_email';
    const TABLE_CUSTOMER_VIDEO_SITE_NAME = 'assol_customer_video_site';
    const TABLE_CUSTOMER_VIDEO_SITE_LINK_NAME = 'assol_customer_video_site_link';
    const TABLE_CUSTOMER_STORY_NAME = 'assol_customer_story';
    const TABLE_CUSTOMER_DOCS_RIGHTS_NAME = 'assol_customer_docs_rights';
    const TABLE_CUSTOMER_MENS_NAME = 'assol_customer_mens';
    const TABLE_CUSTOMER_SITE_STATS_NAME = 'assol_customer_site_stats';
    const TABLE_CUSTOMER_CONTACTS_NAME = 'assol_customer_contacts';

    const TABLE_EMPLOYEE_NAME = 'assol_employee';
    const TABLE_EMPLOYEE_HISTORY_NAME = 'assol_employee_history';
    const TABLE_EMPLOYEE_AGREEMENT_NAME = 'assol_employee_agreement';
    const TABLE_EMPLOYEE_PASSPORT_SCAN_NAME = 'assol_employee_passport_scan';
    const TABLE_EMPLOYEE_CHILDREN_NAME = 'assol_employee_children';
    const TABLE_EMPLOYEE_RELATIVE_NAME = 'assol_employee_relative';
    const TABLE_EMPLOYEE_PHONE_NAME = 'assol_employee_phone';
    const TABLE_EMPLOYEE_EMAIL_NAME = 'assol_employee_email';
    const TABLE_EMPLOYEE_SKYPE_NAME = 'assol_employee_skype';
    const TABLE_EMPLOYEE_SITE_NAME = 'assol_employee_site';
    const TABLE_EMPLOYEE_SITE_CUSTOMER_NAME = 'assol_employee_site_customer';
    const TABLE_EMPLOYEE_SOCNET_NAME = 'assol_employee_socnet';
    const TABLE_EMPLOYEE_ONLINE_NAME = 'assol_employee_online';
    const TABLE_EMPLOYEE_RIGHTS_NAME = 'assol_employee_rights';

    const TABLE_DOCUMENT_NAME = 'assol_document';
    const TABLE_DOCUMENT_RIGHTS_NAME = 'assol_document_rights';

    const TABLE_IMAGE_NAME = 'assol_images';
    const TABLE_IMAGE_MEN_NAME = 'assol_image_men';
    const TABLE_IMAGE_SITE_NAME = 'assol_image_site';

    const TABLE_CHAT_NAME = 'assol_chat';
    const TABLE_CHAT_USER_NAME = 'assol_chat_user';
    const TABLE_CHAT_MESSAGE_NAME = 'assol_chat_message';

    const TABLE_MESSAGE_NAME = 'assol_message';
    const TABLE_MESSAGE_IMAGE_NAME = 'assol_message_image';

    const TABLE_NEWS_NAME = 'assol_news';
    const TABLE_NEWS_READ_NAME = 'assol_news_read';

    const TABLE_REPORT_DAILY_NAME = 'assol_report_daily';
    const TABLE_REPORT_MAILING_NAME = 'assol_report_mailing';
    const TABLE_REPORT_MAILING_INFO_NAME = 'assol_report_mailing_info';
    const TABLE_REPORT_CORRESPONDENCE_INFO_NAME = 'assol_report_correspondence_info';
    const TABLE_REPORT_CORRESPONDENCE_NAME = 'assol_report_correspondence';
    const TABLE_REPORT_SALARY_NAME = 'assol_report_salary';
    const TABLE_REPORT_OVERLAY_SALARY_NAME = 'assol_report_overlay_salary';
    const TABLE_REPORT_GENERAL_SALARY_NAME = 'assol_report_general_salary';
    const TABLE_REPORT_LOVESTORY_MOUNT_NAME = 'assol_report_lovestory_mount';
    const TABLE_REPORT_LOVESTORY_MOUNT_PLAN_NAME = 'assol_report_lovestory_mount_plan';
    const TABLE_REPORT_LOVESTORY_MOUNT_PLAN_AGENCY_NAME = 'assol_report_lovestory_mount_plan_agency';

    const TABLE_SCHEDULE_NAME = 'assol_schedule';
    const TABLE_SETTING_NAME = 'assol_settings';
    const TABLE_SERVICE_WESTERN_NAME = 'assol_service_western';
    const TABLE_SERVICE_MEETING_NAME = 'assol_service_meeting';
    const TABLE_SERVICE_DELIVERY_NAME = 'assol_service_delivery';
    const TABLE_SERVICE_DELIVERY_2_IMAGE_NAME = 'assol_service_delivery2image';

    const TABLE_SITE_NAME = 'assol_sites';

    const TABLE_TASK_NAME = 'assol_tasks';
    const TABLE_TASK_COMMENT = 'assol_task_comment';
    const TABLE_TASK_COMMENT_READ = 'assol_task_comment_read';

    const TABLE_TRAINING_NAME = 'assol_training';
    const TABLE_TRAINING_RIGHTS_NAME = 'assol_training_rights';

    const TABLE_CARD_NAME = 'assol_card';

    /**
     * MY_Model constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * @return CI_DB_query_builder
     */
    protected function db() {
        if (!isset($this->db)) {
            $this->load->database();
        }

        return $this->db;
    }

}