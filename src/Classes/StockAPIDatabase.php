<?php
namespace Catzedonis\Fmp\Classes;

use DateTime;

if (!defined('ABSPATH')) exit;

if (!class_exists('StockAPIDatabase'))
{
    class StockAPIDatabase
    {
        private $table_name;
        protected $API_KEY = '0029cee0ac53fb6876dcf1be0bdf07d1';
        private $channel;
        public function __construct($table)
        {
            $this->table_name = $table;
            $this->create_financial_table();

        }

        /**
         *
         */
        public function create_financial_table()
        {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $this->table_name (
		symbol VARCHAR(50) NOT NULL PRIMARY KEY UNIQUE,
		price DOUBLE NOT NULL,
		price_change_percent DOUBLE NOT NULL,
		yearly_range VARCHAR(100),
		beta FLOAT NOT NULL,
		volume_avg INT NOT NULL,
		market_capitalization DOUBLE NOT NULL,
		last_dividend DOUBLE NOT NULL,
        company_name VARCHAR(225) NOT NULL,
        company_logo VARCHAR(225),
        exchange VARCHAR(150),
        description TEXT,
        industry VARCHAR(150),
        sector VARCHAR(150),
        ceo_name VARCHAR(150),
        website_url VARCHAR(150),
        last_update datetime DEFAULT CURRENT_TIMESTAMP NOT NULL
	
	) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $success = maybe_create_table( $this->table_name,$sql );
            if($success)
            {
                add_option('stock_info_last_update', "N/A");
                //todo hook this up with an auto refresh job
                add_option('stock_info_update_frequency', 6);
            }



        }

        public function set_curl_url($url)
        {
            curl_setopt($this->channel, CURLOPT_URL, $url);
        }
        public function update_stock_company_info($searchValues): string
        {
            $last_update = get_option('stock_info_last_update');
            $returnString = '';
            foreach($searchValues as $search)
            {

                set_time_limit(0);
                $search = $search;
                $profile_url = sprintf("https://financialmodelingprep.com/api/v3/profile/%s?apikey=%s",$search,$this->API_KEY);
                $this->set_curl_url($profile_url);

                $output = curl_exec($this->channel);

                if (curl_error($this->channel))
                {
                    return 'error:' . curl_error($this->channel);
                } else
                {

                    global $wpdb;

                    $date = new DateTime();
                    $last_update = $date->format('Y-m-d H:i:s');
                    $outputJSON = json_decode($output, true);
                    if (empty($outputJSON) || $output == '[]')
                    {

                        $returnString .= " No Data Returned from financialmodelingprep.com for [{$search}]<br/>";
                    }
                    if (isset($outputJSON["Error Message"]))
                    {
                        $returnString .= " [{$search}] " . $outputJSON["Error Message"] . "<br>";
                    }
                    else
                    {
                        foreach ($outputJSON as $stock) {
                            $wpdb->replace($this->table_name, array(
                                'symbol' => $stock['symbol'],
                                'company_name' => $stock['companyName'],
                                'company_logo' => $stock['image'],
                                'exchange' => $stock['exchange'],
                                'description' => $stock['description'],
                                'industry' => $stock['industry'],
                                'sector' => $stock['sector'],
                                'ceo_name' => $stock['ceo'],
                                'website_url' => $stock['website'],
                                'price' => $stock['price'],
                                'price_change_percent' => $stock['changes'],
                                'yearly_range' => $stock['range'],
                                'beta' => $stock['beta'],
                                'volume_avg' => $stock['volAvg'],
                                'market_capitalization' => $stock['mktCap'],
                                'last_dividend' => $stock['lastDiv'],
                                'last_update' => $last_update
                            ));

                        }
                        $returnString .= "[{$search}] Updated Successfully.<br>";
                    }
                }

                update_option('stock_info_last_update', $last_update);
            }
            return $returnString;
        }
        public function set_curl_info()
        {
            set_time_limit(0);

            $channel = curl_init();

            curl_setopt($channel, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($channel, CURLOPT_HEADER, 0);
            curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($channel, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($channel, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($channel, CURLOPT_TIMEOUT, 0);
            curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($channel, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, FALSE);
            return $channel;
        }

        public function update_stock_db()
        {
            $this->channel = $this->set_curl_info();
            $searchValues = get_terms( 'stocks', array('hide_empty' => false));
            return $this->update_stock_company_info(wp_list_pluck($searchValues,'slug'));
        }

        /**
         *
         */
        public function deactivate()
        {
            delete_option( 'stock_info_last_update' );
            delete_option( 'stock_info_update_frequency' );
        }
    }
}