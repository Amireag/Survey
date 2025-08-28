<?php
class I18n {
    private static $instance = null;
    private $currentLanguage = 'en';
    private $translations = [];
    private $rtlLanguages = ['fa', 'ar', 'he'];
    
    private function __construct() {
        $this->loadTranslations();
        $this->detectLanguage();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function detectLanguage() {
        // Check URL parameter first
        if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fa'])) {
            $this->currentLanguage = $_GET['lang'];
            $_SESSION['language'] = $this->currentLanguage;
            return;
        }
        
        // Check session
        if (isset($_SESSION['language']) && in_array($_SESSION['language'], ['en', 'fa'])) {
            $this->currentLanguage = $_SESSION['language'];
            return;
        }
        
        // Check browser language
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (in_array($browserLang, ['en', 'fa'])) {
                $this->currentLanguage = $browserLang;
                $_SESSION['language'] = $this->currentLanguage;
            }
        }
    }
    
    private function loadTranslations() {
        $this->translations = [
            'en' => [
                // Navigation
                'dashboard' => 'Dashboard',
                'surveys' => 'Surveys',
                'create_survey' => 'Create Survey',
                'users' => 'Users',
                'settings' => 'Settings',
                'logout' => 'Logout',
                'login' => 'Login',
                'register' => 'Register',
                
                // Common
                'save' => 'Save',
                'cancel' => 'Cancel',
                'delete' => 'Delete',
                'edit' => 'Edit',
                'view' => 'View',
                'create' => 'Create',
                'update' => 'Update',
                'submit' => 'Submit',
                'close' => 'Close',
                'yes' => 'Yes',
                'no' => 'No',
                'loading' => 'Loading...',
                'error' => 'Error',
                'success' => 'Success',
                'warning' => 'Warning',
                'info' => 'Info',
                
                // Authentication
                'email_or_phone' => 'Email or Phone',
                'password' => 'Password',
                'confirm_password' => 'Confirm Password',
                'login_title' => 'Login to Your Account',
                'register_title' => 'Create New Account',
                'forgot_password' => 'Forgot Password?',
                'remember_me' => 'Remember Me',
                'login_failed' => 'Invalid credentials',
                'registration_success' => 'Account created successfully',
                'logout_success' => 'Logged out successfully',
                
                // Dashboard
                'welcome_back' => 'Welcome Back',
                'total_users' => 'Total Users',
                'total_surveys' => 'Total Surveys',
                'total_responses' => 'Total Responses',
                'credits_used' => 'Credits Used',
                'credits_available' => 'Credits Available',
                'credits_bought' => 'Credits Bought',
                'recent_surveys' => 'Recent Surveys',
                'recent_responses' => 'Recent Responses',
                'top_users' => 'Top Users',
                'top_surveys' => 'Top Surveys',
                
                // Survey Management
                'survey_title' => 'Survey Title',
                'survey_description' => 'Survey Description',
                'survey_status' => 'Status',
                'survey_responses' => 'Responses',
                'survey_created' => 'Created',
                'survey_updated' => 'Updated',
                'draft' => 'Draft',
                'active' => 'Active',
                'closed' => 'Closed',
                'publish_survey' => 'Publish Survey',
                'close_survey' => 'Close Survey',
                'duplicate_survey' => 'Duplicate Survey',
                'export_results' => 'Export Results',
                'view_results' => 'View Results',
                'share_survey' => 'Share Survey',
                'survey_link' => 'Survey Link',
                'copy_link' => 'Copy Link',
                'link_copied' => 'Link copied to clipboard',
                
                // Survey Builder
                'add_question' => 'Add Question',
                'question_text' => 'Question Text',
                'question_type' => 'Question Type',
                'text_input' => 'Text Input',
                'textarea' => 'Text Area',
                'radio_buttons' => 'Radio Buttons',
                'checkboxes' => 'Checkboxes',
                'dropdown' => 'Dropdown',
                'required_question' => 'Required Question',
                'add_option' => 'Add Option',
                'option_text' => 'Option Text',
                'remove_option' => 'Remove Option',
                'move_up' => 'Move Up',
                'move_down' => 'Move Down',
                'remove_question' => 'Remove Question',
                'drag_drop_survey_builder' => 'Drag & Drop Survey Builder',
                'question_types' => 'Question Types',
                'drag_questions_to_add' => 'Drag question types to add them to your survey',
                'short_text_response' => 'Short text response',
                'long_text_response' => 'Long text response',
                'single_choice_response' => 'Single choice response',
                'multiple_choice_response' => 'Multiple choice response',
                'dropdown_selection' => 'Dropdown selection',
                'survey_settings' => 'Survey Settings',
                'enter_survey_title' => 'Enter survey title',
                'enter_survey_description' => 'Enter survey description',
                'allow_anonymous_responses' => 'Allow anonymous responses',
                'show_progress_bar' => 'Show progress bar',
                'preview' => 'Preview',
                'save_survey' => 'Save Survey',
                'untitled_survey' => 'Untitled Survey',
                'survey_description_placeholder' => 'Add a description for your survey',
                'no_questions_yet' => 'No questions yet',
                'drag_question_types_to_start' => 'Drag question types from the sidebar to start building your survey',
                'drop_question_here' => 'Drop question here',
                'question_properties' => 'Question Properties',
                'select_question_to_edit' => 'Select a question to edit its properties',
                'click_question_to_edit_properties' => 'Click on a question to edit its properties',
                'edit_question' => 'Edit Question',
                'save_changes' => 'Save Changes',
                'survey_preview' => 'Survey Preview',
                'text_input_placeholder' => 'Your answer here',
                'textarea_placeholder' => 'Your detailed answer here',
                'select_option' => 'Select an option',
                'required' => 'Required',
                'answer_options' => 'Answer Options',
                'duplicate' => 'Duplicate',
                'copy' => 'Copy',
                'text_question_default' => 'What is your opinion?',
                'textarea_question_default' => 'Please provide detailed feedback',
                'radio_question_default' => 'Which option do you prefer?',
                'checkbox_question_default' => 'Select all that apply',
                'dropdown_question_default' => 'Choose from the options',
                'new_question' => 'New Question',
                'option_1' => 'Option 1',
                'option_2' => 'Option 2',
                'question_updated' => 'Question updated successfully',
                'confirm_delete_question' => 'Are you sure you want to delete this question?',
                'survey_title_required' => 'Survey title is required',
                'error_saving_survey' => 'Error saving survey',
                'survey_published' => 'Survey published successfully',
                'submit_survey' => 'Submit Survey',
                
                // User Management
                'create_user' => 'Create User',
                'user_email' => 'Email',
                'user_phone' => 'Phone',
                'user_role' => 'Role',
                'user_subscription' => 'Subscription Level',
                'user_credits' => 'Credits',
                'user_created' => 'Created',
                'add_credits' => 'Add Credits',
                'subscription_level_1' => 'Basic',
                'subscription_level_2' => 'Standard',
                'subscription_level_3' => 'Premium',
                'subscription_level_4' => 'Professional',
                'subscription_level_5' => 'Enterprise',
                
                // Themes
                'themes' => 'Themes',
                'theme_manager' => 'Theme Manager',
                'predefined_themes' => 'Predefined Themes',
                'custom_themes' => 'Custom Themes',
                'create_theme' => 'Create Theme',
                'theme_name' => 'Theme Name',
                'primary_color' => 'Primary Color',
                'secondary_color' => 'Secondary Color',
                'background_color' => 'Background Color',
                'text_color' => 'Text Color',
                'border_color' => 'Border Color',
                'theme_preview' => 'Theme Preview',
                'apply_theme' => 'Apply Theme',
                'delete_theme' => 'Delete Theme',
                
                // Statistics
                'statistics' => 'Statistics',
                'users_by_subscription' => 'Users by Subscription',
                'surveys_by_status' => 'Surveys by Status',
                'responses_over_time' => 'Responses Over Time',
                'credit_usage' => 'Credit Usage',
                'platform_overview' => 'Platform Overview',
                
                // Messages
                'survey_created_success' => 'Survey created successfully',
                'survey_updated_success' => 'Survey updated successfully',
                'survey_deleted_success' => 'Survey deleted successfully',
                'user_created_success' => 'User created successfully',
                'credits_added_success' => 'Credits added successfully',
                'theme_created_success' => 'Theme created successfully',
                'theme_deleted_success' => 'Theme deleted successfully',
                'insufficient_credits' => 'Insufficient credits',
                'survey_not_found' => 'Survey not found',
                'access_denied' => 'Access denied',
                'invalid_request' => 'Invalid request',
                'database_error' => 'Database error occurred',
                
                // Footer
                'all_rights_reserved' => 'All rights reserved',
                'powered_by' => 'Powered by',
                'privacy_policy' => 'Privacy Policy',
                'terms_of_service' => 'Terms of Service',
                'contact_us' => 'Contact Us',
                
                // Survey Taking
                'survey_completed' => 'Survey Completed',
                'thank_you_message' => 'Thank you for your participation!',
                'survey_closed_message' => 'This survey is currently closed.',
                'survey_not_available' => 'Survey not available',
                'required_field' => 'This field is required',
                'invalid_email' => 'Please enter a valid email address',
                'survey_progress' => 'Survey Progress',
                
                // Homepage
                'create_beautiful_surveys' => 'Create Beautiful Surveys',
                'powerful_survey_platform_description' => 'Build, distribute, and analyze professional surveys with our powerful platform.',
                'get_started_free' => 'Get Started Free',
                'sign_in' => 'Sign In',
                'unlimited_responses' => 'Unlimited Responses',
                'custom_themes' => 'Custom Themes',
                'real_time_analytics' => 'Real-time Analytics',
                'powerful_features' => 'Powerful Features',
                'everything_you_need_for_surveys' => 'Everything you need to create and manage surveys',
                'drag_drop_builder' => 'Drag & Drop Builder',
                'create_surveys_easily' => 'Create surveys easily with our intuitive interface',
                'custom_branding' => 'Custom Branding',
                'brand_your_surveys' => 'Brand your surveys with custom themes and colors',
                'advanced_analytics' => 'Advanced Analytics',
                'detailed_insights' => 'Get detailed insights from your survey responses',
                'secure_reliable' => 'Secure & Reliable',
                'enterprise_security' => 'Enterprise-grade security and reliability',
                'easy_sharing' => 'Easy Sharing',
                'share_surveys_anywhere' => 'Share your surveys anywhere with secure links',
                'multilingual' => 'Multilingual',
                'support_multiple_languages' => 'Support for multiple languages and RTL text',
                'ready_to_get_started' => 'Ready to Get Started?',
                'join_thousands_of_users' => 'Join thousands of users creating amazing surveys',
                'start_free_trial' => 'Start Free Trial',
                'view_demo' => 'View Demo',
                'login_success' => 'Login successful',
                
                // Error Pages
                'page_not_found' => 'Page Not Found',
                'page_not_found_description' => 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.',
                'access_forbidden_description' => 'You do not have permission to access this resource. Please log in or contact support if you believe this is an error.',
                'go_home' => 'Go Home',
                'go_back' => 'Go Back',
                
                // Additional translations
                'about_us' => 'About Us',
                'features' => 'Features', 
                'pricing' => 'Pricing',
                'help_center' => 'Help Center',
                'api_documentation' => 'API Documentation',
                'legal' => 'Legal',
                'cookie_policy' => 'Cookie Policy',
                'support' => 'Support',
                
                // Admin specific
                'main_menu' => 'Main Menu',
                'management' => 'Management',
                'refresh' => 'Refresh',
                'creator' => 'Creator'
            ],
            'fa' => [
                // Navigation
                'dashboard' => 'داشبورد',
                'surveys' => 'نظرسنجی‌ها',
                'create_survey' => 'ایجاد نظرسنجی',
                'users' => 'کاربران',
                'settings' => 'تنظیمات',
                'logout' => 'خروج',
                'login' => 'ورود',
                'register' => 'ثبت نام',
                
                // Common
                'save' => 'ذخیره',
                'cancel' => 'لغو',
                'delete' => 'حذف',
                'edit' => 'ویرایش',
                'view' => 'نمایش',
                'create' => 'ایجاد',
                'update' => 'بروزرسانی',
                'submit' => 'ارسال',
                'close' => 'بستن',
                'yes' => 'بله',
                'no' => 'خیر',
                'loading' => 'در حال بارگذاری...',
                'error' => 'خطا',
                'success' => 'موفقیت',
                'warning' => 'هشدار',
                'info' => 'اطلاعات',
                
                // Authentication
                'email_or_phone' => 'ایمیل یا شماره تلفن',
                'password' => 'رمز عبور',
                'confirm_password' => 'تأیید رمز عبور',
                'login_title' => 'ورود به حساب کاربری',
                'register_title' => 'ایجاد حساب کاربری جدید',
                'forgot_password' => 'فراموشی رمز عبور؟',
                'remember_me' => 'مرا به خاطر بسپار',
                'login_failed' => 'اطلاعات ورود نامعتبر است',
                'registration_success' => 'حساب کاربری با موفقیت ایجاد شد',
                'logout_success' => 'با موفقیت خارج شدید',
                
                // Dashboard
                'welcome_back' => 'خوش آمدید',
                'total_users' => 'کل کاربران',
                'total_surveys' => 'کل نظرسنجی‌ها',
                'total_responses' => 'کل پاسخ‌ها',
                'credits_used' => 'اعتبار استفاده شده',
                'credits_available' => 'اعتبار موجود',
                'credits_bought' => 'اعتبار خریداری شده',
                'recent_surveys' => 'نظرسنجی‌های اخیر',
                'recent_responses' => 'پاسخ‌های اخیر',
                'top_users' => 'کاربران برتر',
                'top_surveys' => 'نظرسنجی‌های برتر',
                
                // Survey Management
                'survey_title' => 'عنوان نظرسنجی',
                'survey_description' => 'توضیحات نظرسنجی',
                'survey_status' => 'وضعیت',
                'survey_responses' => 'پاسخ‌ها',
                'survey_created' => 'ایجاد شده',
                'survey_updated' => 'بروزرسانی شده',
                'draft' => 'پیش‌نویس',
                'active' => 'فعال',
                'closed' => 'بسته شده',
                'publish_survey' => 'انتشار نظرسنجی',
                'close_survey' => 'بستن نظرسنجی',
                'duplicate_survey' => 'کپی نظرسنجی',
                'export_results' => 'خروجی نتایج',
                'view_results' => 'نمایش نتایج',
                'share_survey' => 'اشتراک‌گذاری نظرسنجی',
                'survey_link' => 'لینک نظرسنجی',
                'copy_link' => 'کپی لینک',
                'link_copied' => 'لینک کپی شد',
                
                // Survey Builder
                'add_question' => 'افزودن سوال',
                'question_text' => 'متن سوال',
                'question_type' => 'نوع سوال',
                'text_input' => 'ورودی متن',
                'textarea' => 'ناحیه متن',
                'radio_buttons' => 'دکمه‌های رادیویی',
                'checkboxes' => 'چک باکس‌ها',
                'dropdown' => 'فهرست کشویی',
                'required_question' => 'سوال اجباری',
                'add_option' => 'افزودن گزینه',
                'option_text' => 'متن گزینه',
                'remove_option' => 'حذف گزینه',
                'move_up' => 'انتقال به بالا',
                'move_down' => 'انتقال به پایین',
                'remove_question' => 'حذف سوال',
                
                // User Management
                'create_user' => 'ایجاد کاربر',
                'user_email' => 'ایمیل',
                'user_phone' => 'تلفن',
                'user_role' => 'نقش',
                'user_subscription' => 'سطح اشتراک',
                'user_credits' => 'اعتبار',
                'user_created' => 'ایجاد شده',
                'add_credits' => 'افزودن اعتبار',
                'subscription_level_1' => 'پایه',
                'subscription_level_2' => 'استاندارد',
                'subscription_level_3' => 'ممتاز',
                'subscription_level_4' => 'حرفه‌ای',
                'subscription_level_5' => 'سازمانی',
                
                // Themes
                'themes' => 'قالب‌ها',
                'theme_manager' => 'مدیریت قالب‌ها',
                'predefined_themes' => 'قالب‌های از پیش تعریف شده',
                'custom_themes' => 'قالب‌های سفارشی',
                'create_theme' => 'ایجاد قالب',
                'theme_name' => 'نام قالب',
                'primary_color' => 'رنگ اصلی',
                'secondary_color' => 'رنگ فرعی',
                'background_color' => 'رنگ پس‌زمینه',
                'text_color' => 'رنگ متن',
                'border_color' => 'رنگ حاشیه',
                'theme_preview' => 'پیش‌نمایش قالب',
                'apply_theme' => 'اعمال قالب',
                'delete_theme' => 'حذف قالب',
                
                // Statistics
                'statistics' => 'آمار',
                'users_by_subscription' => 'کاربران بر اساس اشتراک',
                'surveys_by_status' => 'نظرسنجی‌ها بر اساس وضعیت',
                'responses_over_time' => 'پاسخ‌ها در طول زمان',
                'credit_usage' => 'استفاده از اعتبار',
                'platform_overview' => 'نمای کلی پلتفرم',
                
                // Messages
                'survey_created_success' => 'نظرسنجی با موفقیت ایجاد شد',
                'survey_updated_success' => 'نظرسنجی با موفقیت بروزرسانی شد',
                'survey_deleted_success' => 'نظرسنجی با موفقیت حذف شد',
                'user_created_success' => 'کاربر با موفقیت ایجاد شد',
                'credits_added_success' => 'اعتبار با موفقیت اضافه شد',
                'theme_created_success' => 'قالب با موفقیت ایجاد شد',
                'theme_deleted_success' => 'قالب با موفقیت حذف شد',
                'insufficient_credits' => 'اعتبار کافی نیست',
                'survey_not_found' => 'نظرسنجی یافت نشد',
                'access_denied' => 'دسترسی مجاز نیست',
                'invalid_request' => 'درخواست نامعتبر',
                'database_error' => 'خطا در پایگاه داده',
                
                // Footer
                'all_rights_reserved' => 'تمامی حقوق محفوظ است',
                'powered_by' => 'قدرت گرفته از',
                'privacy_policy' => 'سیاست حفظ حریم خصوصی',
                'terms_of_service' => 'شرایط خدمات',
                'contact_us' => 'تماس با ما',
                
                // Survey Taking
                'survey_completed' => 'نظرسنجی تکمیل شد',
                'thank_you_message' => 'از مشارکت شما متشکریم!',
                'survey_closed_message' => 'این نظرسنجی در حال حاضر بسته است.',
                'survey_not_available' => 'نظرسنجی در دسترس نیست',
                'required_field' => 'این فیلد اجباری است',
                'invalid_email' => 'لطفاً آدرس ایمیل معتبری وارد کنید',
                'survey_progress' => 'پیشرفت نظرسنجی'
            ]
        ];
    }
    
    public function t($key, $params = []) {
        $translation = $this->translations[$this->currentLanguage][$key] ?? $this->translations['en'][$key] ?? $key;
        
        // Replace parameters in translation
        foreach ($params as $param => $value) {
            $translation = str_replace('{{' . $param . '}}', $value, $translation);
        }
        
        return $translation;
    }
    
    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }
    
    public function setLanguage($language) {
        if (in_array($language, ['en', 'fa'])) {
            $this->currentLanguage = $language;
            $_SESSION['language'] = $language;
        }
    }
    
    public function isRTL() {
        return in_array($this->currentLanguage, $this->rtlLanguages);
    }
    
    public function getDirection() {
        return $this->isRTL() ? 'rtl' : 'ltr';
    }
    
    public function getFontFamily() {
        return $this->currentLanguage === 'fa' ? 'Morabba' : 'Poppins';
    }
    
    public function getLanguageSwitchUrl($targetLanguage) {
        $currentUrl = $_SERVER['REQUEST_URI'];
        $urlParts = parse_url($currentUrl);
        
        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $params);
        } else {
            $params = [];
        }
        
        $params['lang'] = $targetLanguage;
        
        $newQuery = http_build_query($params);
        $newUrl = $urlParts['path'] . ($newQuery ? '?' . $newQuery : '');
        
        return $newUrl;
    }
}

// Global function for easy translation
function t($key, $params = []) {
    return I18n::getInstance()->t($key, $params);
}

// Global function for language direction
function i18n_dir() {
    return I18n::getInstance()->getDirection();
}

// Global function for font family
function font() {
    return I18n::getInstance()->getFontFamily();
}
?>
