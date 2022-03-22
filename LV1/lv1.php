<!DOCTYPE html>
<html>
        <head>
        </head>

        <body>
        <?php                
		include('simple_html_dom.php');
                
                // Sučelje iRadovi sa metodama create,save i read;

                interface iRadovi {
                        public function create($data);
                        public function save();
                        public function read();
                }

                // Klasa DiplomskiRad koja implementira sučelje iRadovi

                class DiplomskiRad implements iRadovi {
                        private $naziv_rada = NULL;
                        private $tekst_rada = NULL;
                        private $link_rada = NULL;
                        private $oib_tvrtke = NULL;


                // Konstruktor

                        function __construct($data) {
                                $this->_id = uniqid();
                                $this->_naziv_rada = $data['naziv_rada'];
                                $this->_tekst_rada = $data['tekst_rada'];
                                $this->_link_rada = $data['link_rada'];
                                $this->_oib_tvrtke = $data['oib_tvrtke'];
                        }

                        function create($data) {
                                self::__construct($data);
                        }

                // Funkcija save pomoću koje se zapis sprema u bazu podataka.

                        function save() {
                                $conn = new mysqli($servername="localhost", $username="test", $password="test", $dbname="radovi");
                                if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                }

                                $id = $this->_id;
                                $naziv = $this->_naziv_rada;
                                $tekst = $this->_tekst_rada;
                                $link = $this->_link_rada;
                                $oib = $this->_oib_tvrtke;

                                $sql = "INSERT INTO diplomski_radovi (id, naziv_rada, tekst_rada, link_rada, oib_tvrtke) VALUES ('$id', '$naziv', '$tekst', '$link', '$oib')";
                                if($conn->query($sql) === true) {
                                       //$this->read();
                                }
                                else {
                                        echo "Error! " . $sql . "<br>" . $conn->error;
                                };
                                $conn->close();
			}

                // Funkcija read radi ispis zapisa iz baze podataka.
                
                        function read() {
                                $conn = new mysqli($servername="localhost", $username="test", $password="test", $dbname="radovi");
                                if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                }

                                $sql = "SELECT * FROM diplomski_radovi";
                                $output = $conn->query($sql);				
                                if ($output->num_rows > 0) {
                                        while($item = $output->fetch_assoc()) {
                                                echo '<div style="border: 2px solid #0000FF; padding: 5px">ID: ' . $item["id"] .
                                                "<br>Naziv rada: " . $item["naziv_rada"] .
                                                "<br>OIB tvrtke: " . $item["oib_tvrtke"] .
                                                "<br>Link rada: <a href=" . $item["link_rada"] .">" . $item["link_rada"] . "</a>".
                                                "<br>Tekst rada: " . $item["tekst_rada"] . "</div><br><br><br>";                                                
                                        }
                                }
                                $conn->close();
                        }

                        
                }

                // Funkcija crawl dohvaća sadržaj sa URL-a koji joj se preda kao argument.

		function crawl($base){
                	$curl = curl_init();
                	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                	curl_setopt($curl, CURLOPT_HEADER, false);
                	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                	curl_setopt($curl, CURLOPT_URL, $base);
                	curl_setopt($curl, CURLOPT_REFERER, $base);
                	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                	$str = curl_exec($curl);
                	curl_close($curl);
                	$html = new simple_html_dom();
                	return $html->load($str);
		}

		$url = 'https://stup.ferit.hr/index.php/zavrsni-radovi/page/2';                

		$html = crawl($url);

                // Izvlačenje potrebnih podataka iz sadržaja stranice, te kreiranje objekta klase DiplomskiRad i pohranjivanje podataka u bazu pomoću save() funkcije.

		foreach($html->find('article') as $article) {
                        foreach($article->find('ul.slides img') as $img) {
                        }
                        foreach($article->find('h2.entry-title a') as $link) {
                        	$html2 = crawl($link->href);
                        	foreach($html2->find('.post-content') as $text) {
                        	}
			}

                	$rad = array(
                		'naziv_rada' => $link->plaintext,
                		'tekst_rada' => $text->plaintext,
                		'link_rada' => $link->href,
                        	'oib_tvrtke' => preg_replace('/[^0-9]/', '', $img->src)
                	);
                	$newRad = new DiplomskiRad($rad);
                	$newRad->save();
		}                
                $newRad->read();                
	?>
        </body>
</html>
