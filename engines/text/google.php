<?php
    class GoogleRequest extends EngineRequest {
        protected string $arc_id;
        protected int $arc_timestamp = 0;

        private function generate_arc_id() {
            $charset = "01234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-";
            $this->arc_id = "srp_";

            for ($i = 0; $i < 24; $i++) {
                $c = random_int(0, strlen($charset) - 1);
                $this->arc_id .= $charset[$c];
            }

            $this->arc_id .= "_1";
            $this->arc_timestamp = time();
        }

        public function get_request_url() {
            if ($this->arc_timestamp + 3600 < time())
                $this->generate_arc_id();

            $query_encoded = str_replace("%22", "\"", urlencode($this->query));
            $results = array();

            $domain = $this->opts->google_domain;
            $results_language = $this->opts->language;
            $number_of_results = $this->opts->number_of_results;
            $arc_page = sprintf("%02d", $this->page * 10);

            $url = "https://www.google.$domain/search?q=$query_encoded&nfpr=1&start=$this->page";

            if (3 > strlen($results_language) && 0 < strlen($results_language)) {
                $url .= "&lr=lang_$results_language";
                $url .= "&hl=$results_language";
            }

            if (3 > strlen($number_of_results) && 0 < strlen($number_of_results))
                $url .= "&num=$number_of_results";

            if (isset($_COOKIE["safe_search"]))
                $url .= "&safe=medium";

            $url .= "&asearch=arc&async=arc_id:$this->arc_id$arc_page,use_ac:true,_fmt:html";

            return $url;
        }


        public function parse_results($response) {
            $results = array();
            $xpath = get_xpath($response);

            if (!$xpath)
                return $results;

            $didyoumean = $xpath->query(".//p[@class='QRYxYe NNMgCf']/a/b/i")[0];

            if (!is_null($didyoumean))
                array_push($results, array(
                    "did_you_mean" => $didyoumean->textContent
                ));

            foreach($xpath->query("//div[@class='MjjYud']") as $result) {
                $url = $xpath->evaluate(".//a[@class='zReHs']/@href", $result)[0];

                if ($url == null)
                    continue;

                if (!empty($results) && array_key_exists("url", end($results)) && end($results)["url"] == $url->textContent)
                    continue;

                $url = $url->textContent;

                $title = $xpath->evaluate(".//h3", $result)[0];
                $description = $xpath->evaluate(".//div[contains(@class, 'VwiC3b')]", $result)[0];

                array_push($results,
                    array (
                        "title" => htmlspecialchars($title->textContent),
                        "url" =>  htmlspecialchars($url),
                        // base_url is to be removed in the future, see #47
                        "base_url" => htmlspecialchars(get_base_url($url)),
                        "description" =>  $description == null ?
                                          TEXTS["result_no_description"] :
                                          htmlspecialchars($description->textContent)
                    )
                );
            }

            if (empty($results) && !str_contains($response, "Our systems have detected unusual traffic from your computer network.")) {
                $results["error"] = array(
                    "message" => TEXTS["failure_empty"]
                );
            }

            return $results;
        }
    }
?>
