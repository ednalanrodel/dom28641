<?php



if (class_exists("GFForms")) {
    GFForms::include_addon_framework();

    class GFTypeformAddon extends GFAddOn
    {

        const ADDON_SLUG = 'typeform-gravity-forms-addon';

        protected $_version = "1.1.1";
        protected $_min_gravityforms_version = "1.9";
        protected $_slug = self::ADDON_SLUG;
        protected $_path = "typeform-gravity-forms-addon/index.php";
        protected $_full_path = __FILE__;
        protected $_title = "Typeform Add-On";
        protected $_short_title = "Typeform";

        public function init()
        {
            parent::init();

            $this->token = $this->get_plugin_setting('typeform-token');

            add_filter('gform_shortcode_form', [$this, 'getTypeform'], 1, 3);

            add_filter('gform_after_save_form', [$this, 'saveTypeformId']);

            if ($this->hasTokenAdded()) {
                $this->api = new TypeformApi($this->token);
                $this->timesSaved = 0;
            } else {
                add_action('admin_notices', [$this, 'checkIfToken']);
            }
        }

        private function hasTokenAdded()
        {
            return ($this->token && !empty($this->token)) ? true: false;
        }

        public function checkIfToken()
        {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php _e( 'Please add your <a href="http://typeform.io/" target="_blank">Typeform Token</a> to make the addon work.', 'typeform' ); ?></p>
            </div>
            <?php
        }

        public function save_form_settings($form, $settings)
        {

            if ($this->isTypeformEnabled($settings)) {
                if ($this->hasTokenAdded()) {
                    $design_id = $this->getDesign($settings, $form);

                    if ($design_id != null) {
                        $settings['design-id'] = $design_id;
                    }
                    $response = $this->getTypeformData($form, $design_id);

                    $settings['form-id'] = $response['uid'];
                    $settings['form-url'] = $response['url'];

                } else {
                    GFCommon::add_error_message(__('Remember to add your Typeform token in order to render an updated/new typeform.'));
                }
            } else {
                $settings['enable-typeform'] = false;
            }

            return parent::save_form_settings($form, $settings);
        }


        private function getDesign($settings, $form)
        {
            try {
                $response = $this->api->getDesignId($settings, $form);
                return $response->id;
            } catch (Exception $e) {
                return null;
            }
            
        }


        public function saveTypeformId($form, $is_new = false)
        {
            $settings = parent::get_form_settings($form);


            if ($this->isTypeformEnabled($settings)) {
                if ($this->hasTokenAdded()) {
                    $response = $this->getTypeformData($form, $settings['design-id']);

                    $form_url = $response['url'];

                    $settings['form-id'] = $response['uid'];
                    $settings['form-url'] = $form_url;

                }
            }

            return parent::save_form_settings($form, $settings);

        }


        private function getTypeformUrl($typeform_links)
        {
            foreach ($typeform_links as $link) {
                if ($link->rel == 'form_render') {
                    return $link->href;
                }
            }
        }

        public function getTypeformData($form, $design_id = null)
        {
            $webhook = apply_filters('typeform/webhook', add_query_arg('typeform-id', $form['id'], TypeformWebHook::getEndpointUrl()));
            $response = $this->api->getFormId($form['fields'], $webhook, $form['title'], ['form-' . $form['id']], $design_id);

            return $this->getTypeformEmbed($response);
        }

        public function getTypeformEmbed($response)
        {
            return [
                'uid'   => $response->id,
                'url'   => $this->getTypeformUrl($response->_links)
            ];
        }

        public function form_settings_fields($form)
        {

            return [
                [
                    "title"  => "Typeform Settings",
                    "fields" => [
                        [
                            "label"   => __("Enable Typeform render", 'tf-gf'),
                            "type"    => "checkbox",
                            "name"    => "enable-typeform",
                            "tooltip" => __("Render form with Typeform", 'tf-gf'),
                            "choices" => [
                                [
                                    "label" => "Enabled",
                                    "name"  => "enable-typeform"
                                ]
                            ],
                            'feedback_callback' => [$this, 'is_valid_setting']
                        ],
                    ]
                ],
                [
                    "title" => "Design",
                    "fields" => [
                        [
                            "label"   => "Design ID",
                            "type"    => "hidden",
                            "name"    => "design-id",
                        ],
                        [
                            "label"   => "Form ID",
                            "type"    => "hidden",
                            "name"    => "form-id",
                        ],
                        [
                            "label"   => "Form URL",
                            "type"    => "hidden",
                            "name"    => "form-url",
                        ],
                        [
                            "label"   => "",
                            "type"    => "hidden",
                            "name"    => "form-typeform-settings",
                            "value"   => "it-is"
                        ],
                        [
                            "label"   => __("Questions color", 'tf-gf'),
                            "type"    => "text",
                            "name"    => "font-color",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "placeholder"   => '#FFFFFF'
                        ],
                        [
                            "label"   => __("Button color", 'tf-gf'),
                            "type"    => "text",
                            "name"    => "button-color",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "placeholder"   => '#FFFFFF'
                        ],
                        [
                            "label"   => __("Answers color", 'tf-gf'),
                            "type"    => "text",
                            "name"    => "answer-color",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "placeholder"   => '#FFFFFF'
                        ]
                        ,
                        [
                            "label"   => __("Background color", 'tf-gf'),
                            "type"    => "text",
                            "name"    => "background-color",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "placeholder"   => '#FFFFFF'
                        ],
                        [
                            "label"   => __("Font Family", 'tf-gf'),
                            "type"    => "select",
                            "name"    => "font-family",
                            "tooltip" => __("HEX color code", 'tf-gf'),
                            "choices"   => [
                                [
                                    "label" => "Acme"
                                ],
                                [
                                    "label" => "Arial"
                                ],
                                [
                                    "label" => "Arvo"
                                ],
                                [
                                    "label" => "Bangers"
                                ],
                                [
                                    "label" => "Cabin"
                                ],
                                [
                                    "label" => "Cabin Condensed"
                                ],
                                [
                                    "label" => "Courier"
                                ],
                                [
                                    "label" => "Crete Round"
                                ],
                                [
                                    "label" => "Dancing Script"
                                ],
                                [
                                    "label" => "Exo"
                                ],
                                [
                                    "label" => "Georgia"
                                ],
                                [
                                    "label" => "Handlee"
                                ],
                                [
                                    "label" => "Karla"
                                ],
                                [
                                    "label" => "Lato"
                                ],
                                [
                                    "label" => "Lobster"
                                ],
                                [
                                    "label" => "Lora"
                                ],
                                [
                                    "label" => "McLaren"
                                ],
                                [
                                    "label" => "Monsterrat"
                                ],
                                [
                                    "label" => "Nixie"
                                ],
                                [
                                    "label" => "Old Standard TT"
                                ],
                                [
                                    "label" => "Nixie"
                                ],
                                [
                                    "label" => "Open Sans"
                                ],
                                [
                                    "label" => "Oswald"
                                ],
                                [
                                    "label" => "Nixie"
                                ],
                                [
                                    "label" => "Playfair"
                                ],
                                [
                                    "label" => "Quicksand"
                                ],
                                [
                                    "label" => "Raleway"
                                ],
                                [
                                    "label" => "Signika"
                                ],
                                [
                                    "label" => "Sniglet"
                                ],
                                [
                                    "label" => "Source Sans Pro"
                                ],
                                [
                                    "label" => "Vollkorn"
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "title" => "Embed",
                    "fields" => [
                        [
                            'label'     => 'Height',
                            'type'      => 'text',
                            'placeholder'   => '500px',
                            'name'      => 'embed-height'
                        ],
                        [
                            'label'     => 'Width',
                            'type'      => 'text',
                            'placeholder'   => '100%',
                            'name'      => 'embed-width'
                        ]
                    ]
                ]
            ];
        }

        public function plugin_settings_fields()
        {
            return [
                [
                    "title"  => __("Typeform Add-On Settings", 'tf-gf'),
                    "fields" => [
                        [
                            "name"    => "typeform-token",
                            "tooltip" => __("Token generated by typeform.io", 'tf-gf'),
                            "label"   => __("Typeform IO Token", 'tf-gf'),
                            "type"    => "text",
                            "class"   => "large",
                        ]
                    ]
                ]
            ];
        }

        public function is_valid_setting($value)
        {
            return $this->hasTokenAdded();
        }

        public function getTypeform($shortcode_string, $attributes, $content)
        {
            $form_id = $attributes['id'];
            $gf = new GFAPI();
            $form = $gf->get_form($form_id);

            $form_settings = $this->get_form_settings($form);

            if ($this->isTypeformEnabled($form_settings) && !empty($form_settings['form-url'])) {
                if (!is_user_logged_in()) {
                    RGFormsModel::insert_form_view($form_id, $_SERVER['REMOTE_ADDR']);
                }
                return $this->embedTypeform($form_settings['form-url'], $form_settings['embed-width'], $form_settings['embed-height']);
            } else {
                return $shortcode_string;
            }
        }

        private function isTypeformEnabled($form_settings)
        {
            return (isset($form_settings['enable-typeform']) && $form_settings['enable-typeform']);
        }

        public function embedTypeform($form_url, $width = '100%', $height = '500px')
        {
            if (empty($height)) {
                $height = '500px';
            }
            if (empty($width)) {
                $width = '100%';
            }
            return '<iframe src="' . $form_url . '" height="' . $height . '" width="' . $width . '" style="border:0;"></iframe>';
        }
    }

    new GFTypeformAddon();
} else {
    add_action('admin_notices', 'installGfTf');

    function installGfTf()
    {
        ?>
        <div class="error notice">
            <p><?php _e( 'Please install Gravity Forms to use this addon', 'typeform' ); ?></p>
        </div>
        <?php
    }
}
