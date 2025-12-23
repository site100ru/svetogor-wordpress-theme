<?php
/**
 * Класс и функции для работы с контактными данными
 * 
 * @package svetogor
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для работы с контактными данными (Singleton)
 */
class Contact_Data_Manager {
    
    private static $instance = null;
    private $phones = null;
    private $socials = null;
    private $company_data_cache = array();
    
    /**
     * Получить экземпляр класса
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Приватный конструктор
     */
    private function __construct() {
        // Singleton pattern
    }
    
    // ========================================================================
    // Работа с телефонами
    // ========================================================================
    
    /**
     * Получить все телефоны (с кешированием)
     */
    public function get_phones() {
        if ($this->phones === null) {
            $this->phones = get_field('company_phones', 'option') ?: array();
        }
        return $this->phones;
    }
    
    /**
     * Получить основной телефон
     */
    public function get_main_phone() {
        $phones = $this->get_phones();
        
        if (!$phones) {
            return '';
        }
        
        foreach ($phones as $phone) {
            if (isset($phone['phone_is_main']) && $phone['phone_is_main']) {
                return $phone['phone_number'];
            }
        }
        
        // Если основной не найден, возвращаем первый
        return isset($phones[0]['phone_number']) ? $phones[0]['phone_number'] : '';
    }
    
    /**
     * Получить данные основного телефона
     */
    public function get_main_phone_data() {
        $phones = $this->get_phones();
        
        if (!$phones) {
            return false;
        }
        
        foreach ($phones as $phone) {
            if (isset($phone['phone_is_main']) && $phone['phone_is_main']) {
                return $phone;
            }
        }
        
        // Если основной не найден, возвращаем первый
        return isset($phones[0]) ? $phones[0] : false;
    }
    
    /**
     * Получить все телефоны для футера
     */
    public function get_footer_phones() {
        $phones = $this->get_phones();
        
        if (!$phones) {
            return array();
        }
        
        $footer_phones = array();
        foreach ($phones as $phone) {
            if (isset($phone['phone_show_footer']) && $phone['phone_show_footer']) {
                $footer_phones[] = $phone;
            }
        }
        
        return $footer_phones;
    }
    
    /**
     * Получить все телефоны для блоков
     */
    public function get_block_phones() {
        $phones = $this->get_phones();
        
        if (!$phones) {
            return array();
        }
        
        $block_phones = array();
        foreach ($phones as $phone) {
            if (isset($phone['phone_show_blocks']) && $phone['phone_show_blocks']) {
                $block_phones[] = $phone;
            }
        }
        
        return $block_phones;
    }
    
    /**
     * Получить телефоны для страницы контактов
     */
    public function get_contact_phones() {
        $phones = $this->get_phones();
        
        if (!$phones) {
            return array();
        }
        
        $contact_phones = array();
        foreach ($phones as $phone) {
            if (isset($phone['phone_show_contacts']) && $phone['phone_show_contacts']) {
                $contact_phones[] = $phone;
            }
        }
        
        return $contact_phones;
    }
    
    /**
     * Получить URL иконки телефона с fallback
     */
    public function get_phone_icon_url($phone_data) {
        if (isset($phone_data['phone_icon']) && $phone_data['phone_icon'] && isset($phone_data['phone_icon']['url'])) {
            return $phone_data['phone_icon']['url'];
        }
        
        return get_contact_icon_url('phone_icon', 'mobile-phone-ico.svg');
    }
    
    // ========================================================================
    // Работа с социальными сетями
    // ========================================================================
    
    /**
     * Получить все соцсети (с кешированием)
     */
    public function get_social_networks() {
        if ($this->socials === null) {
            $this->socials = get_field('social_networks', 'option') ?: array();
        }
        return $this->socials;
    }
    
    /**
     * Получить соцсети для футера
     */
    public function get_footer_socials() {
        $socials = $this->get_social_networks();
        
        if (!$socials) {
            return array();
        }
        
        $footer_socials = array();
        foreach ($socials as $social) {
            if (isset($social['show_in_footer']) && $social['show_in_footer']) {
                $footer_socials[] = $social;
            }
        }
        
        return $footer_socials;
    }
    
    /**
     * Получить соцсети для блоков
     */
    public function get_block_socials() {
        $socials = $this->get_social_networks();
        
        if (!$socials) {
            return array();
        }
        
        $block_socials = array();
        foreach ($socials as $social) {
            if (isset($social['show_in_blocks']) && $social['show_in_blocks']) {
                $block_socials[] = $social;
            }
        }
        
        return $block_socials;
    }
    
    /**
     * Получить соцсети для header
     */
    public function get_header_socials() {
        $socials = $this->get_social_networks();
        
        if (!$socials) {
            return array();
        }
        
        $header_socials = array();
        foreach ($socials as $social) {
            if (isset($social['show_in_header']) && $social['show_in_header']) {
                $header_socials[] = $social;
            }
        }
        
        return $header_socials;
    }
    
    /**
     * Получить соцсети для контактов
     */
    public function get_contact_socials() {
        $socials = $this->get_social_networks();
        
        if (!$socials) {
            return array();
        }
        
        $contact_socials = array();
        foreach ($socials as $social) {
            if (isset($social['show_in_contacts']) && $social['show_in_contacts']) {
                $contact_socials[] = $social;
            }
        }
        
        return $contact_socials;
    }
    
    // ========================================================================
    // Данные компании
    // ========================================================================
    
    /**
     * Получить данные компании с кешированием
     */
    public function get_company_data($field_name, $default = '') {
        if (!isset($this->company_data_cache[$field_name])) {
            $this->company_data_cache[$field_name] = get_option_field($field_name, $default);
        }
        
        return $this->company_data_cache[$field_name];
    }
    
    /**
     * Получить название компании
     */
    public function get_company_name() {
        return $this->get_company_data('company_name', 'ИП Авинников Евгений Максимович');
    }
    
    /**
     * Получить ИНН компании
     */
    public function get_company_inn() {
        return $this->get_company_data('company_inn', '450127005482');
    }
    
    /**
     * Получить время работы
     */
    public function get_work_hours() {
        return $this->get_company_data('company_work_hours', 'Пн-Пт: 10:00-18:00. Сб, Вс: Выходной');
    }
    
    /**
     * Получить юридический адрес
     */
    public function get_legal_address() {
        return $this->get_company_data('company_legal_address', '141700, Россия, Московская обл., г. Долгопрудный, Лихачевский пр-кт, дом 76, корпус 1, квартира 76.');
    }
    
    /**
     * Получить адрес компании
     */
    public function get_company_address() {
        $address = $this->get_company_data('company_address', '');
        
        if (!empty($address)) {
            return $address;
        }
        
        // Fallback
        return 'г. Москва, ул. Полярная, 31В, оф. 141';
    }
    
    /**
     * Получить email компании
     */
    public function get_company_email() {
        return $this->get_company_data('company_email', '');
    }
    
    /**
     * Получить ОГРН компании
     */
    public function get_company_ogrn() {
        return $this->get_company_data('company_ogrn', '');
    }
    
    /**
     * Получить расчетный счет компании
     */
    public function get_company_bank_account() {
        return $this->get_company_data('company_bank_account', '');
    }
    
    /**
     * Получить БИК банка
     */
    public function get_company_bank_bik() {
        return $this->get_company_data('company_bank_bik', '');
    }
    
    /**
     * Получить название банка
     */
    public function get_company_bank_name() {
        return $this->get_company_data('company_bank_name', '');
    }
    
    /**
     * Получить корреспондентский счет
     */
    public function get_company_bank_correspondent_account() {
        return $this->get_company_data('company_bank_correspondent_account', '');
    }
    
    /**
     * Сбросить кеш
     */
    public function clear_cache() {
        $this->phones = null;
        $this->socials = null;
        $this->company_data_cache = array();
    }
}

// ============================================================================
// Обратная совместимость - функции-обертки
// ============================================================================

/**
 * Получить основной телефон
 */
function get_main_phone() {
    return Contact_Data_Manager::get_instance()->get_main_phone();
}

/**
 * Получить данные основного телефона
 */
function get_main_phone_data() {
    return Contact_Data_Manager::get_instance()->get_main_phone_data();
}

/**
 * Получить телефоны для футера
 */
function get_footer_phones() {
    return Contact_Data_Manager::get_instance()->get_footer_phones();
}

/**
 * Получить телефоны для блоков
 */
function get_block_phones() {
    return Contact_Data_Manager::get_instance()->get_block_phones();
}

/**
 * Получить телефоны для контактов
 */
function get_contacts_phones() {
    return Contact_Data_Manager::get_instance()->get_contact_phones();
}

/**
 * Получить URL иконки телефона
 */
function get_phone_icon_url($phone_data) {
    return Contact_Data_Manager::get_instance()->get_phone_icon_url($phone_data);
}

/**
 * Получить соцсети для футера
 */
function get_footer_social_networks() {
    return Contact_Data_Manager::get_instance()->get_footer_socials();
}

/**
 * Получить соцсети для блоков
 */
function get_block_social_networks() {
    return Contact_Data_Manager::get_instance()->get_block_socials();
}

/**
 * Получить соцсети для контактов
 */
function get_contacts_social_networks() {
    return Contact_Data_Manager::get_instance()->get_contact_socials();
}

/**
 * Получить соцсети для header
 */
function get_header_social_networks() {
    return Contact_Data_Manager::get_instance()->get_header_socials();
}

/**
 * Получить название компании
 */
function get_company_name() {
    return Contact_Data_Manager::get_instance()->get_company_name();
}

/**
 * Получить ИНН компании
 */
function get_company_inn() {
    return Contact_Data_Manager::get_instance()->get_company_inn();
}

/**
 * Получить время работы
 */
function get_company_work_hours() {
    return Contact_Data_Manager::get_instance()->get_work_hours();
}

/**
 * Получить юридический адрес
 */
function get_company_legal_address() {
    return Contact_Data_Manager::get_instance()->get_legal_address();
}

/**
 * Получить адрес компании
 */
function get_company_address() {
    return Contact_Data_Manager::get_instance()->get_company_address();
}

/**
 * Получить email компании
 */
function get_company_email() {
    return Contact_Data_Manager::get_instance()->get_company_email();
}

/**
 * Получить ОГРН компании
 */
function get_company_ogrn() {
    return Contact_Data_Manager::get_instance()->get_company_ogrn();
}

/**
 * Получить расчетный счет компании
 */
function get_company_bank_account() {
    return Contact_Data_Manager::get_instance()->get_company_bank_account();
}

/**
 * Получить БИК банка
 */
function get_company_bank_bik() {
    return Contact_Data_Manager::get_instance()->get_company_bank_bik();
}

/**
 * Получить название банка
 */
function get_company_bank_name() {
    return Contact_Data_Manager::get_instance()->get_company_bank_name();
}

/**
 * Получить корреспондентский счет
 */
function get_company_bank_correspondent_account() {
    return Contact_Data_Manager::get_instance()->get_company_bank_correspondent_account();
}