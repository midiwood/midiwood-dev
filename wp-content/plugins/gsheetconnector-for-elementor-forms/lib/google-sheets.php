<?php

if ( !defined( 'ABSPATH' ) )
   exit;

include_once ( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

class GSC_Elementor_Free {

   private $token;
   private $spreadsheet;
   private $worksheet;

  // const clientId = '1021473022177-5p6d9m3bh34fukon3car31qvfl7khrju.apps.googleusercontent.com';
   //const clientSecret = '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';

   const clientId = '343006833383-ajjmvck7167u5omiu6kflkmpd7455mo3.apps.googleusercontent.com';
   const clientSecret = 'wjSapQopzEaql23EbFNF1Bjk';


   private static $instance;

   public function __construct() {
      
   }

   public static function setInstance( Google_Client $instance = null ) {
      self::$instance = $instance;
   }

   public static function getInstance() {
      if ( is_null( self::$instance ) ) {
         throw new LogicException( "Invalid Client" );
      }

      return self::$instance;
   }

   //constructed on call
   public static function preauth( $access_code ) {
      $client = new Google_Client();
      $client->setClientId( GSC_Elementor_Free::clientId );
      $client->setClientSecret( GSC_Elementor_Free::clientSecret );
      $client->setRedirectUri('https://oauth.gsheetconnector.com');
      $client->setScopes( Google_Service_Sheets::SPREADSHEETS );
      $client->setScopes( Google_Service_Drive::DRIVE_METADATA_READONLY );
      $client->setAccessType( 'offline' );
      $client->fetchAccessTokenWithAuthCode( $access_code );
      $tokenData = $client->getAccessToken();

      GSC_Elementor_Free::updateToken( $tokenData );
   }


   //constructed on call
    public static function preauth_manual($access_code, $client_id, $secret_id, $redirect_url)
    {
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($secret_id);
        $client->setRedirectUri($redirect_url);
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
        $client->setAccessType('offline');
        $client->fetchAccessTokenWithAuthCode($access_code);
        $tokenData = $client->getAccessToken();
        GSC_Elementor_Free::updateToken_manual($tokenData);
    }

    public static function updateToken_manual($tokenData)
    {
         $expires_in = isset( $tokenData['expires_in'] ) ? intval( $tokenData['expires_in'] ) : 0;
         $tokenData['expire'] = time() + $expires_in;
        try {
           $tokenJson = json_encode( $tokenData );

            //resolved - google sheet permission issues - start
           if(isset($tokenData['scope'])){
            $permission = explode(" ", $tokenData['scope']);
            if((in_array("https://www.googleapis.com/auth/drive.metadata.readonly",$permission)) && (in_array("https://www.googleapis.com/auth/spreadsheets",$permission))) {
               update_option('elefgs_verify', 'valid');
            }else{
              update_option('elefgs_verify', 'invalid-auth');
            }
          }
           //resolved - google sheet permission issues - END
           update_option('elefgs_token_manual', $tokenJson);
        } catch (Exception $e) {
            GsEl_Connector_Utility::ele_gs_debug_log("Token write fail! - " . $e->getMessage());
        }
    }

   public static function updateToken( $tokenData ) {
      $expires_in = isset( $tokenData['expires_in'] ) ? intval( $tokenData['expires_in'] ) : 0;
      $tokenData['expire'] = time() + $expires_in;
      try {
         $tokenJson = json_encode( $tokenData );
       
        //resolved - google sheet permission issues - start
         if(isset($tokenData['scope'])){
            $permission = explode(" ", $tokenData['scope']);
            if((in_array("https://www.googleapis.com/auth/drive.metadata.readonly",$permission)) && (in_array("https://www.googleapis.com/auth/spreadsheets",$permission))) {
              update_option('elefgs_verify', 'valid');
            }else{
            	update_option('elefgs_verify', 'invalid-auth');
            }
         }
          //resolved - google sheet permission issues - END
           update_option( 'elefgs_token', $tokenJson );
           
        } catch ( Exception $e ) {
         GsEl_Connector_Utility::ele_gs_debug_log( "Token write fail! - " . $e->getMessage() );
      }
   }

   public function auth() {

      $maunal_setting = get_option('elefgs_manual_setting') ? get_option('elefgs_manual_setting') : '0';

     if ((isset($maunal_setting)) && ($maunal_setting == '1'))
         $tokenData = json_decode(get_option('elefgs_token_manual'), true);
     else
         $tokenData = json_decode(get_option('elefgs_token'), true);

      
      if ( !isset( $tokenData['refresh_token'] ) || empty( $tokenData['refresh_token'] ) ) {
         throw new LogicException( "Auth, Invalid OAuth2 access token" );
         exit();
      }

      try {
         $client = new Google_Client();

         if ($maunal_setting == '1') {
             $elefgs_client_id = get_option('elefgs_client_id');
             $elefgs_secret_id = get_option('elefgs_secret_id');
             $client->setClientId($elefgs_client_id);
             $client->setClientSecret($elefgs_secret_id);
         }else {
            $client->setClientId( GSC_Elementor_Free::clientId );
            $client->setClientSecret( GSC_Elementor_Free::clientSecret );
         }

         
         $client->setScopes( Google_Service_Sheets::SPREADSHEETS );
         $client->setScopes( Google_Service_Drive::DRIVE_METADATA_READONLY );
         $client->refreshToken( $tokenData['refresh_token'] );
         $client->setAccessType( 'offline' );
         //GSC_Elementor_Free::updateToken( $tokenData );

         if($maunal_setting == '1')
            GSC_Elementor_Free::updateToken_manual( $tokenData );
         else
            GSC_Elementor_Free::updateToken( $tokenData );
         

         self::setInstance( $client );
      } catch ( Exception $e ) {
         throw new LogicException( "Auth, Error fetching OAuth2 access token, message: " . $e->getMessage() );
         exit();
      }
   }

   public function get_user_data() {
      $client = self::getInstance();

      $results = $this->get_spreadsheets();

      echo '<pre>';
      print_r( $results );
      echo '</pre>';
      $spreadsheets = $this->get_worktabs( '1mRuDMnZveDFQrmzHM9s5YkPA4F_dZkHJ1Gh81BvYB2k' );
      echo '<pre>';
      print_r( $spreadsheets );
      echo '</pre>';
      $this->setSpreadsheetId( '1mRuDMnZveDFQrmzHM9s5YkPA4F_dZkHJ1Gh81BvYB2k' );
      $this->setWorkTabId( 'Foglio1' );
      $worksheetTab = $this->list_rows();
      echo '<pre>';
      print_r( $worksheetTab );
      echo '</pre>';
   }

   //preg_match is a key of error handle in this case
   public function setSpreadsheetId( $id ) {
      $this->spreadsheet = $id;
   }

   public function getSpreadsheetId() {

      return $this->spreadsheet;
   }

   public function setWorkTabId( $id ) {
      $this->worksheet = $id;
   }

   public function getWorkTabId() {
      return $this->worksheet;
   }

   public function add_row( $data ) {
      try {
      
         $client = self::getInstance();
         $service = new Google_Service_Sheets( $client );
         $spreadsheetId = $this->getSpreadsheetId();
         $work_sheets = $service->spreadsheets->get( $spreadsheetId );

         if ( !empty( $work_sheets ) && !empty( $data ) ) {
            foreach ( $work_sheets as $sheet ) {
               $properties = $sheet->getProperties();
               $sheet_id = $properties->getSheetId();

               $worksheet_id = $this->getWorkTabId();

               if ( $sheet_id == $worksheet_id ) {
                  $worksheet_id = $properties->getTitle();
                  $worksheetCell = $service->spreadsheets_values->get( $spreadsheetId, $worksheet_id . "!1:1" );
                  $insert_data = array();
                  if ( isset( $worksheetCell->values[0] ) ) {
                     foreach ( $worksheetCell->values[0] as $k => $name ) {
                        if ( isset( $data[$name] ) && $data[$name] != '' ) {
                           $insert_data[] = $data[$name];
                        } else {
                           $insert_data[] = '';
                        }
                     }
                  }
				  
				  
				  /*RASHID*/
					$tab_name = $worksheet_id;
					$full_range = $tab_name."!A1:Z";
					$response   = $service->spreadsheets_values->get( $spreadsheetId, $full_range );
					$get_values = $response->getValues();
					
					if( $get_values) {
						$row  = count( $get_values ) + 1;
					}
					else {
						$row = 1;
					}
					$range = $tab_name."!A".$row.":Z"; 
				  
                  $range_new = $worksheet_id;

                  // Create the value range Object
                  $valueRange = new Google_Service_Sheets_ValueRange();

                  // set values of inserted data
                  $valueRange->setValues( ["values" => $insert_data ] );
                  
                  // Add two values
                  // Then you need to add configuration
                  $conf = ["valueInputOption" => "USER_ENTERED", "insertDataOption" => "INSERT_ROWS"];
                  $conf = ["valueInputOption" => "USER_ENTERED" ];

                  // append the spreadsheet(add new row in the sheet)
                  // $result = $service->spreadsheets_values->append( $spreadsheetId, $range_new, $valueRange, $conf );
                  $result = $service->spreadsheets_values->append( $spreadsheetId, $range, $valueRange, $conf );
               }
            }
         }
      } catch ( Exception $e ) {
         return null;
         exit();
      }
   }

   public function add_multiple_row( $data ) {
      try {
         $client = self::getInstance();
         $service = new Google_Service_Sheets( $client );
         $spreadsheetId = $this->getSpreadsheetId();
         $work_sheets = $service->spreadsheets->get( $spreadsheetId );

         if ( !empty( $work_sheets ) && !empty( $data ) ) {
            foreach ( $work_sheets as $sheet ) {
               $properties = $sheet->getProperties();
               $sheet_id = $properties->getSheetId();

               $worksheet_id = $this->getWorkTabId();

               if ( $sheet_id == $worksheet_id ) {
                  $worksheet_id = $properties->getTitle();
                  $worksheetCell = $service->spreadsheets_values->get( $spreadsheetId, $worksheet_id . "!1:1" );
                  $insert_data = array();
                  $final_data = array();
                  if ( isset( $worksheetCell->values[0] ) ) {
                     foreach ( $data as $key => $value ) {
                        foreach ( $worksheetCell->values[0] as $k => $name ) {
                           if ( isset( $value[$name] ) && $value[$name] != '' ) {
                              $insert_data[] = $value[$name];
                           } else {
                              $insert_data[] = '';
                           }
                        }
                        $final_data[] = $insert_data;
                        unset( $insert_data );
                     }
                  }
                  
                  /*RASHID*/
					$tab_name = $worksheet_id;
					$full_range = $tab_name."!A1:Z";
					$response   = $service->spreadsheets_values->get( $spreadsheetId, $full_range );
					$get_values = $response->getValues();
					
					if( $get_values) {
						$row  = count( $get_values ) + 1;
					}
					else {
						$row = 1;
					}
					$range = $tab_name."!A".$row.":Z";

                  $sheet_values = $final_data;

                  if ( !empty( $sheet_values ) ) {
                     $requestBody = new Google_Service_Sheets_ValueRange( [
                        'values' => $sheet_values
                             ] );

                     $params = [
                        'valueInputOption' => 'USER_ENTERED'
                     ];
                     $response = $service->spreadsheets_values->append( $spreadsheetId, $range, $requestBody, $params );
                  }
               }
            }
         }
      } catch ( Exception $e ) {
         return null;
         exit();
      }
   }

   //get all the spreadsheets
   public function get_spreadsheets() {
      $all_sheets = array();
      try {
         $client = self::getInstance();

         $service = new Google_Service_Drive( $client );

         $optParams = array(
            'q' => "mimeType='application/vnd.google-apps.spreadsheet'"
         );
         $results = $service->files->listFiles( $optParams );
         foreach ( $results->files as $spreadsheet ) {
            if ( isset( $spreadsheet['kind'] ) && $spreadsheet['kind'] == 'drive#file' ) {
               $all_sheets[] = array(
                  'id' => $spreadsheet['id'],
                  'title' => $spreadsheet['name'],
               );
            }
         }
      } catch ( Exception $e ) {
         return null;
         exit();
      }
      return $all_sheets;
   }

   //get worksheets title
   public function get_worktabs($spreadsheet_id) {

      $work_tabs_list = array();
      try {
         $client = self::getInstance();
         $service = new Google_Service_Sheets($client);
         $work_sheets = $service->spreadsheets->get($spreadsheet_id);

         foreach ($work_sheets as $sheet) {
            $properties = $sheet->getProperties();
            $id = $properties->getSheetId();
            $title = $properties->getTitle();
            $work_tabs_list[$id] = $title;
         }
      } catch (Exception $e) {
         return null;
         exit();
      }

      return $work_tabs_list;
   }



   /**
    * Function - Adding custom column header to the sheet
    * @param string $sheet_name
    * @param string $tab_name
    * @param array $gs_map_tags 
    * @since 1.0
    */
   public function add_header($spreadsheetId, $tab_name, $row_data, $is_header=false) {

         if( $is_header ) {

            $client = self::getInstance();
            $service = new Google_Service_Sheets($client);
            $range = $tab_name . '!1:1';

            $valueRange = new Google_Service_Sheets_ValueRange();
            $valueRange->setValues(["values" => $row_data]);
            $conf = ["valueInputOption" => "RAW"];
            $result = $service->spreadsheets_values->update($spreadsheetId, $range, $valueRange, $conf);
         }
   }
	
	
	
	
	/*******************************************************************************/
	/********************************  VERSION 3.1 *********************************/
	/*******************************************************************************/
	
	
	/** 
	* GSC_Elementor_Free::get_sheet_name
	* get WorkSheet Name
	* @since 3.1 
	* @param string $spreadsheet_id
	* @param string $tab_id
	* @retun string $tab_name
	**/
	public function get_sheet_name( $spreadsheet_id, $tab_id ) {
		
		$all_sheet_data = get_option( 'elefgs_sheetId' );
		
		$tab_name = "";
		foreach( $all_sheet_data as $spreadsheet ) {
			
			if( $spreadsheet['id'] == $spreadsheet_id ) {
				$tabs = $spreadsheet['tabId'];
				
				foreach( $tabs as $name => $id ) {
					if( $id == $tab_id ) {
						$tab_name = $name;
					}
				}
			}
		}
		
		return $tab_name;
	}
	
	
	/** 
	* GSC_Elementor_Free::get_sheet_name
	* get SpreadSheet Name
	* @since 3.1 
	* @param string $spreadsheet_id
	* @retun string $spreadsheetName
	**/
	public function get_spreadsheet_name( $spreadsheet_id ) {
		
		$all_sheet_data = get_option( 'elefgs_sheetId' );
		
		$spreadsheetName = "";
		foreach( $all_sheet_data as $spreadsheet_name => $spreadsheet ) {
			
			if( $spreadsheet['id'] == $spreadsheet_id ) {
				$spreadsheetName = $spreadsheet_name;
			}
		}
		
		return $spreadsheetName;
	}

   public function add_row_feed($spreadsheet_id, $sheet_title, $data, $append = true) {
       try {
           $client = self::getInstance();
           $service = new Google_Service_Sheets($client);
           $work_sheets = $service->spreadsheets->get($spreadsheet_id)->getSheets();

           if (!empty($work_sheets) && !empty($data)) {
               foreach ($work_sheets as $sheet) {
                   $properties = $sheet->getProperties();
                   $sheet_id = $properties->getSheetId();
                   $worksheet_id = $this->getWorkTabId();

                   if ($sheet_id == $worksheet_id) {
                       // Fetch headers
                       $worksheetCell = $service->spreadsheets_values->get($spreadsheet_id, $sheet_title . '!1:1');
                       $insert_data = [];

                       // Debugging headers
                       // error_log('Headers in Google Sheet: ' . print_r($worksheetCell->values[0], true));

                       if (isset($worksheetCell->values[0])) {
                           $headers = $worksheetCell->values[0];

                           // Map the $data to the headers
                           foreach ($headers as $header) {
                               // Use the header as the key to find the corresponding value
                               $insert_data[] = isset($data[$header]) ? $data[$header] : '';
                           }
                       } else {
                           error_log('No headers found in the first row of the sheet.');
                           return;
                       }

                       // Append data to the sheet
                       $valueRange = new Google_Service_Sheets_ValueRange();
                       $valueRange->setValues([$insert_data]);

                       $conf = ['valueInputOption' => 'USER_ENTERED'];
                       $response = $service->spreadsheets_values->append($spreadsheet_id, $sheet_title . '!A1', $valueRange, $conf);

                       // Debug the response
                       // error_log('Append response: ' . print_r($response, true));
                   }
               }
           }
       } catch (Exception $e) {
           error_log("Error appending data to Google Sheets: " . $e->getMessage());
           return null;
       }
   }
	
	/** 
	* GSC_Elementor_Free::add_row_to_sheet
	* Send row data to sheet
	* @since 3.1 
	* @param string $spreadsheet_id
	* @param string $tab_name
	* @param array $row_data
	* @param array $is_header
	* @retun bool $result
	**/
	public function add_row_to_sheet( $spreadsheet_id, $tab_name, $row_data, $is_header = false ) {
		
		if( ! $row_data ) {
			return;
		}
		
		ksort($row_data);
		
		try {			
			$client = self::getInstance();	
			
			if( ! $client ) {
				return false;
			}
					
			$service = new Google_Service_Sheets($client);
			
			
			$full_range = $tab_name."!A1:Z";
			$response   = $service->spreadsheets_values->get( $spreadsheet_id, $full_range );
			$get_values = $response->getValues();
			
			if( $get_values) {
				$row  = count( $get_values ) + 1;
			}
			else {
				$row = 1;
			}
			
			if( $is_header ) {
				$row = 1;
			}
			
			$range = $tab_name."!A".$row.":Z";
			
			
			foreach($row_data as &$data) {
				$data = str_replace( "{row}", $row, $data );
			}
			
			$valueRange = new Google_Service_Sheets_ValueRange();
			$valueRange->setValues(["values" => $row_data]);
			
			
			$conf = ["valueInputOption" => "USER_ENTERED", "insertDataOption" => "INSERT_ROWS"];
			$result = $service->spreadsheets_values->append($spreadsheet_id, $range, $valueRange, $conf);	
			return true;
		} 
		catch (Exception $e) {
			GsEl_Connector_Utility::ele_gs_debug_log( __METHOD__ . " Error while adding row to sheet." . $e->getMessage() );
			return false;
		}
	}
	
	
	/** 
	* GSC_Elementor_Free::get_header_row
	* Send row data to sheet
	* @since 3.1 
	* @param string $spreadsheet_id
	* @param string $tab_id
	* @retun array $header_cells
	**/
	public function get_header_row( $spreadsheet_id, $tab_id ) {
		
		$header_cells = array();
		try {
		
			$client = $this->getInstance();			
			
			if( ! $client ) {
				return false;
			}			
			
			$service = new Google_Service_Sheets($client);
			
			$work_sheets = $service->spreadsheets->get($spreadsheet_id);
			
			if( $work_sheets ) {
				
				foreach ($work_sheets as $sheet) {
				
					$properties = $sheet->getProperties();
					$work_sheet_id = $properties->getSheetId();
					
					if( $work_sheet_id == $tab_id ) {
						
						$tab_title = $properties->getTitle();
						$header_row = $service->spreadsheets_values->get($spreadsheet_id, $tab_title . "!1:1");
						
						$header_row_values = $header_row->getValues();
						
						if( isset( $header_row_values[0] ) && $header_row_values[0] ) {
							$header_cells = $header_row_values[0];
						}		
					}
				}
			}
		}
		catch (Exception $e) {
			$header_cells = array();
			return $header_cells;
		}
		
		return $header_cells;
	}
	
	
	/** 
	* GSC_Elementor_Free::sort_sheet_by_column
	* Sort Sheet by column Index
	* @since 3.1 
	* @param string $spreadsheet_id
	* @param string $tab_id
	* @param int $column_index
	* @param string $sort_order
	* @retun bool $result
	**/
	public function sort_sheet_by_column( $spreadsheet_id, $tab_id, $column_index, $sort_order = "ASCENDING" ) {
		
		try {
			if( $column_index !== false && is_numeric($column_index) ) {			
				$client = $this->getInstance();
				
				if( ! $client ) {
					return false;
				}
				
				$service = new Google_Service_Sheets($client);
				
				$args = array(
					"sortRange" => array(
						'range' => array(
							'sheetId' => $tab_id,
							'startRowIndex' => 1,
							'startColumnIndex' => 0,
						),
						'sortSpecs' => array(
							array(
								'sortOrder' => $sort_order,
								'dimensionIndex' => $column_index,
							),							
						),
					)
				);
				
				$google_service_sheet_request = new Google_Service_Sheets_Request( $args );				
				$request = array( $google_service_sheet_request );				
				$args = array( "requests" => $request );
				$batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest( $args );
				$result = $service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
				return true;
			}
		}
		
		catch (Exception $e) {
			GsEl_Connector_Utility::ele_gs_debug_log( __METHOD__ . " Error in sorting of sheet." . $e->getMessage() );
			return false;
		}
	}
	
	
	/** 
	* GSC_Elementor_Free::hex_color_to_google_rgb
	* Function to convert hex to rgb for google
	* @since 3.1 
	* @param string $hex
	* @retun array $rgba
	**/
	function hex_color_to_google_rgb($hex) {
	
		$rgb_return = array();
		
		$hex      = str_replace('#', '', $hex);
		$length   = strlen($hex);
		$rgb['red'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
		$rgb['green'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
		$rgb['blue'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
		
		foreach( $rgb as $key => $clr ) {
			$rgb_return[$key] = $clr / 255;
		}
		return $rgb_return;
	}
	
	
	/** 
	* GSC_Elementor_Free::freeze_row
	* Freeze the header
	* @since 3.1 
	* @param string $spreadsheet_id
	* @param string $tab_id
	* @param int $number_of_rows
	* @retun bool $result
	**/
	public function freeze_row( $spreadsheet_id, $tab_id, $number_of_rows = 1 ) {
	
		$number_of_rows = apply_filters( "gsheet_default_frozen_rows", $number_of_rows );
		
		try {
			$client = $this->getInstance();	
			
			if( ! $client ) {
				return false;
			}
			
			$service = new Google_Service_Sheets($client);
			$args = array(
				"updateSheetProperties" => array(
					'fields' => 'gridProperties.frozenRowCount',
					'properties' => [
						'sheetId' => $tab_id,
						'gridProperties' => array(
							'frozenRowCount' => $number_of_rows
						),
					],
				)
			);
			
			$google_service_sheet_request = new Google_Service_Sheets_Request( $args );				
			$request = array( $google_service_sheet_request );				
			$args = array( "requests" => $request );
			$batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest( $args );
			$result = $service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
			return true;
		}
		catch (Exception $e) {
			GsEl_Connector_Utility::ele_gs_debug_log( __METHOD__ . " Error in freezing header rows." . $e->getMessage() );
			return false;
		}
	}

	
	/** 
	* GSC_Elementor_Free::set_alternate_colors
	* Set alternate colors
	* @since 3.1 
	* @param string $spreadsheet_id
	* @param string $tab_id
	* @param string $headerColor
	* @param string $oddColor
	* @param string $evenColor
	* @retun bool $result
	**/
	public function set_alternate_colors( $spreadsheet_id, $tab_id, $headerColor, $oddColor, $evenColor ) {
	
		try {
			$client = $this->getInstance();	
			
			if( ! $client ) {
				return false;
			}
			
			$service = new Google_Service_Sheets($client);
			$work_sheets = $service->spreadsheets->get($spreadsheet_id);
			
			$range = array( 'sheetId' => $tab_id );
			$args = array();
			
			$range_exist = false;
			
			$rowProperties = array();
			$rowProperties["headerColor"] = $headerColor ? $this->hex_color_to_google_rgb($headerColor) : $this->hex_color_to_google_rgb("#ffffff");
			$rowProperties["firstBandColor"] = $oddColor ? $this->hex_color_to_google_rgb($oddColor) : $this->hex_color_to_google_rgb("#ffffff");
			$rowProperties["secondBandColor"] = $evenColor ? $this->hex_color_to_google_rgb($evenColor) : $this->hex_color_to_google_rgb("#ffffff");
			
			$banded_range_id = 100;
			if( $tab_id != 0 ) {
				$generate_banded_range_id = substr($tab_id, 0, 4);
				$banded_range_id = $generate_banded_range_id;
			}
			
			$banding_request = array(	
				"bandedRange" => array(
					"bandedRangeId" => $banded_range_id,
					"range" => $range,
					"rowProperties" => $rowProperties,
				)
			);	
			
			
			foreach ($work_sheets as $sheet) {
				$properties = $sheet->getProperties();			
				if( $properties->sheetId == $tab_id ) {				
					$bandedRanges = $sheet->getBandedRanges();
					foreach( $bandedRanges as $bandedRange	 ) {					
						if( $bandedRange->bandedRangeId == $banded_range_id ) {
							$range_exist = true;
						}
					}
				}
			}
			
			if( $range_exist ) {
				$args['updateBanding'] = $banding_request;
				$args['updateBanding']['fields'] = "*";
			}
			else {
				$args['addBanding'] = $banding_request;
			}
			
			$banding_request = new Google_Service_Sheets_Request( $args );	
			$request = array( $banding_request );			
			
			$args = array( "requests" => $request );
			$batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest( $args );
			$result = $service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
			return true;
		}
		catch (Exception $e) {
			GsEl_Connector_Utility::ele_gs_debug_log( __METHOD__ . " Error in setting alternate colors." . $e->getMessage() );
			return false;
		}
	}

	
	/** 
	* GSC_Elementor_Free::remove_alternate_colors
	* Remove alternate colors
	* @since 3.1 
	* @param string $spreadsheet_id
	* @param string $tab_id
	* @retun bool $result
	**/
	public function remove_alternate_colors( $spreadsheet_id, $tab_id ) {
	
		try {
			$client = $this->getInstance();	
			
			if( ! $client ) {
				return false;
			}
			
			$service = new Google_Service_Sheets($client);
			$work_sheets = $service->spreadsheets->get($spreadsheet_id);
			
			$range = array( 'sheetId' => $tab_id );
			$args = array();
			
			$range_exist = false;
			
			$banded_range_id = 100;
			if( $tab_id != 0 ) {
				$generate_banded_range_id = substr($tab_id, 0, 4);
				$banded_range_id = $generate_banded_range_id;
			}
			
			foreach ($work_sheets as $sheet) {
				$properties = $sheet->getProperties();			
				if( $properties->sheetId == $tab_id ) {				
					$bandedRanges = $sheet->bandedRanges;				
					foreach( $bandedRanges as $bandedRange	 ) {					
						if( $bandedRange->bandedRangeId == $banded_range_id ) {
							$range_exist = true;
						}
					}
				}
			}
			
			if( $range_exist ) {
				$args = array( 
					'deleteBanding' => array(
						"bandedRangeId" => $banded_range_id,
					)
				);
				
				$banding_request = new Google_Service_Sheets_Request( $args );	
				$request = array( $banding_request );			
				
				$args = array( "requests" => $request );
				$batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest( $args );
				$result = $service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
				return true;
			}
		}
		catch (Exception $e) {
			GsEl_Connector_Utility::ele_gs_debug_log( __METHOD__ . " Error in removing alternate colors." . $e->getMessage() );
			return false;
		}
	}
	
	/** 
	* GSC_Elementor_Free::sync_with_google_account
	* Fetch Spreadsheets
	* @since 3.1 
	**/
	public function sync_with_google_account() {
		return;
		
		$return_ajax = false;
		
		if ( isset( $_POST['isajax'] ) && $_POST['isajax'] == 'yes' ) {
			check_ajax_referer( 'gf-ajax-nonce', 'security' );
			$init = sanitize_text_field( $_POST['isinit'] );
			$return_ajax = true;
		}
		
		$worksheet_array = array();
		$sheetdata = array();
		$doc = new GSC_Elementor_Free();
		$doc->auth();
		$spreadsheetFeed = $doc->get_spreadsheets();
		
		if( ! $spreadsheetFeed ) {
			return false;
		}
		
		foreach ( $spreadsheetFeed as $sheetfeeds ) {
			$sheetId = $sheetfeeds['id'];
			$sheetname = $sheetfeeds['title'];

			$worksheetFeed = $doc->get_worktabs( $sheetId );

			foreach ( $worksheetFeed as $worksheet ) {
				$tab_id = $worksheet['id'];
				$tab_name = $worksheet['title'];
				$worksheet_array[] = $tab_name;
				$worksheet_ids[$tab_name] = $tab_id;
			}

			$sheetId_array[$sheetname] = array(
			"id" => $sheetId,
			"tabId" => $worksheet_ids
			);

			unset( $worksheet_ids );
			$sheetdata[$sheetname] = $worksheet_array;
			unset( $worksheet_array );
		}

		update_option( 'elefgs_sheetId', $sheetId_array );
		update_option( 'elefgs_feeds', $sheetdata );

		if ( $return_ajax == true ) {
			if ( $init == 'yes' ) {
				wp_send_json_success( array( "success" => 'yes' ) );
			} 
			else {
				wp_send_json_success( array( "success" => 'no' ) );
			}
		}
	}
	
	
	/** 
	* GSC_Elementor_Free::gsheet_get_google_account
	* Get Google Account
	* @since 3.1 
	* @retun $user
	**/
	public function gsheet_get_google_account() {		
	
		try {
			$client = $this->getInstance();
			
			if( ! $client ) {
				return false;
			}
			
			$service = new Google_Service_Oauth2($client);
			$user = $service->userinfo->get();			
		}
		catch (Exception $e) {
			GsEl_Connector_Utility::ele_gs_debug_log( __METHOD__ . " Error in fetching user info: \n " . $e->getMessage() );
			return false;
		}
		
		return $user;
	}
	
	
	/** 
	* GSC_Elementor_Free::gsheet_get_google_account_email
	* Get Google Account Email
	* @since 3.1 
	* @retun string $email
	**/
	public function gsheet_get_google_account_email() {		
		$google_account = $this->gsheet_get_google_account();	
		
		if( $google_account ) {
			return $google_account->email;
		}
		else {
			return "";
		}
	}
	
	
	/** 
	* GSC_Elementor_Free::gsheet_print_google_account_email
	* Get Google Account Email
	* @since 3.1 
	* @retun string $google_account
	**/
	public function gsheet_print_google_account_email() {
		try{		
			$google_account = get_option("gsc_elementor_email_account");
			
			if( $google_account ) {
				return $google_account;
			}
			else {
				
				$google_sheet = new GSC_Elementor_Free();
				$google_sheet->auth();				 
				$email = $google_sheet->gsheet_get_google_account_email();
				
				return $email;
			}
		}catch(Exception $e){
			return false;
		 } 		
	}
	
	/** 
	* GSC_Elementor_Free::gsheet_print_google_account_email
	* Get Google Account Email
	* @since 3.1 
	* @param string $sheet_title
	* @retun array $response
	**/
	public function gsheet_create_google_sheet($sheet_title = "") {
	
		$response = false;
		
		try {
			$client = $this->getInstance();
			
			if( ! $client ) {
				return false;
			}
			
			
			$title = $sheet_title ? $sheet_title : "GSheetConnector Elementor";
			
			$properties = new Google_Service_Sheets_SpreadsheetProperties();
			$properties->setTitle($title);

			$spreadsheet = new Google_Service_Sheets_Spreadsheet();
			$spreadsheet->setProperties($properties);

			$sheet_service = new Google_Service_Sheets($client);		
			$create_spreadsheet = $sheet_service->spreadsheets->create( $spreadsheet );
			
			$spreadsheet = array(
				"spreadsheet_id" => $create_spreadsheet->spreadsheetId,
				"spreadsheet_name" => $title,
				"spreadsheet" => $create_spreadsheet,
				
			);
			$response = array( "result" => true, "spreadsheet" => $spreadsheet );
			
			do_action("gsheet_after_create_google_sheet", $response);
			$this->update_google_spreadsheets_option( $create_spreadsheet->spreadsheetId, $sheet_title );
		}
		catch (Exception $e) {
			$response = array( "result" => false, "error" => $e->getMessage() );
			GsEl_Connector_Utility::ele_gs_debug_log( __METHOD__ . " Error in creating google sheet: \n " . $e->getMessage() );
		}
		
		return $response;
	}
	
	
	public function update_google_spreadsheets_option( $spreadsheet_id, $sheet_title ) {
		
		$gfgs_sheetId = get_option( 'elefgs_sheetId' );
		$gfgs_feeds = get_option( 'elefgs_feeds' );
		
		if( ! $gfgs_sheetId ) {
			$gfgs_sheetId = array();
		}
		if( ! $gfgs_feeds ) {
			$gfgs_feeds = array();
		}
		
		$gfgs_sheetId[$sheet_title] = array(
			"id" => $spreadsheet_id,
			"tabId" => array(
				"Sheet1" => 0
			),
		);
		
		$gfgs_feeds[$sheet_title] = array(
			"0" => "Sheet1",
		);
		
		update_option( 'elefgs_sheetId', $gfgs_sheetId );
		update_option( 'elefgs_feeds', $gfgs_feeds );
		
	}

   /** 
       * GFGSC_googlesheet::gsheet_print_google_account_email
       * Get Google Account Email
       * @since 3.1 
       * @retun string $google_account
       **/
       public function gsheet_print_google_account_email_manual() {
          try{
             $google_account = get_option("elefgs_email_account_manual");
             if( false && $google_account ) {
                return $google_account;
             }
             else {
                
                $google_sheet = new GSC_Elementor_Free();
                $google_sheet->auth();            
                $email = $google_sheet->gsheet_get_google_account_email();
                update_option("elefgs_email_account_manual", $email);
                return $email;
             }
          }catch(Exception $e){
             return false;
          }    
               
       }


       /**
     * Generate token for the user and refresh the token if it's expired.
     *
     * @return array
     */
    public static function getClient_auth($flag = 0, $ele_clientId = '', $ele_clientSecert = '')
    {
        $ele_client = new Google_Client();
        $ele_client->setApplicationName('Manage ele Elementor Forms with Google Spreadsheet');
        $ele_client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
        $ele_client->setScopes(Google_Service_Drive::DRIVE_METADATA_READONLY);
        $ele_client->addScope(Google_Service_Sheets::SPREADSHEETS);
        $ele_client->addScope( 'https://www.googleapis.com/auth/userinfo.email' );
        $ele_client->setClientId($ele_clientId);
        $ele_client->setClientSecret($ele_clientSecert);
        $ele_client->setRedirectUri(esc_html(admin_url('admin.php?page=gsheetconnector-elementor-config')));
        $ele_client->setAccessType('offline');
        $ele_client->setApprovalPrompt('force');
        try {
            if (empty($ele_auth_token)) {
                $ele_auth_url = $ele_client->createAuthUrl();
                return $ele_auth_url;
            }
            if (!empty($ele_gscele_accessToken)) {
                $ele_accessToken = json_decode($ele_gscele_accessToken, true);
            } else {
                if (empty($ele_auth_token)) {
                    $ele_auth_url = $ele_client->createAuthUrl();
                    return $ele_auth_url;
                }
            }

            $ele_client->setAccessToken($ele_accessToken);
            // Refresh the token if it's expired.
            if ($ele_client->isAccessTokenExpired()) {
                // save refresh token to some variable
                $ele_refreshTokenSaved = $ele_client->getRefreshToken();
                $ele_client->fetchAccessTokenWithRefreshToken($ele_client->getRefreshToken());
                // pass access token to some variable
                $ele_accessTokenUpdated = $ele_client->getAccessToken();
                // append refresh token
                $ele_accessTokenUpdated['refresh_token'] = $ele_refreshTokenSaved;
                //Set the new acces token
                $ele_accessToken = $ele_refreshTokenSaved;
                gscele::gscele_update_option('elesheets_google_accessToken', json_encode($ele_accessTokenUpdated));
                $ele_accessToken = json_decode(json_encode($ele_accessTokenUpdated), true);
                $ele_client->setAccessToken($ele_accessToken);
            }
        } catch (Exception $e) {
            if ($flag) {
                return $e->getMessage();
            } else {
                return false;
            }
        }
        return $ele_client;
    }


   public function getTabIdBySheetId($sheetId, $sheetData){

   }
}
