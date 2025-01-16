jQuery(document).ready(function () {
    /**
     * verify the api code
     * @since 1.0
     */
    jQuery(document).on('click', '#save-ele-code', function () {
        jQuery(this).parent().children(".loading-sign").addClass("loading");
        var data = {
            action: 'verify_ele_integation',
            code: jQuery('#ele-code').val(),
            security: jQuery('#gs-ajax-nonce-ele').val()
        };
        jQuery.post(ajaxurl, data, function (response) {
            if (response == -1) {
                return false; // Invalid nonce
            }

            if (!response.success) {
                jQuery(".loading-sign").removeClass("loading");
                jQuery("#gs-validation-message").empty();
                jQuery("<span class='error-message'>Invalid Access code entered.</span>").appendTo('#gs-validation-message');
            } else {
                jQuery(".loading-sign").removeClass("loading");
                jQuery("#gs-validation-message").empty();
                jQuery("<span class='gs-valid-message'>Your Google Access Code is Authorized and Saved.</span> <br/><br/><span class='wp-valid-notice'> Note: If you are getting any errors or not showing sheet in dropdown, then make sure to check the debug log. To contact us for any issues do send us your debug log.</span>").appendTo('#gs-validation-message');
                // setTimeout(function () {
                //     location.reload();
                // }, 1000);
                setTimeout(function () { 
                    window.location.href = jQuery("#redirect_auth_eleforms").val();
                 }, 1000);
            }
        });

    });

    /**
     * deactivate the api code
     * @since 1.0
     */
    jQuery(document).on('click', '#deactivate-log-ele', function () {
        jQuery(".loading-sign-deactive").addClass("loading");
        var txt;
        var r = confirm("Are You sure you want to deactivate Google Integration ?");
        if (r == true) {
            var data = {
                action: 'deactivate_ele_integation',
                security: jQuery('#gs-ajax-nonce-ele').val()
            };
            jQuery.post(ajaxurl, data, function (response) {
                if (response == -1) {
                    return false; // Invalid nonce
                }

                if (!response.success) {
                    alert('Error while deactivation');
                    jQuery(".loading-sign-deactive").removeClass("loading");
                    jQuery("#deactivate-message").empty();

                } else {
                    jQuery(".loading-sign-deactive").removeClass("loading");
                    jQuery("#deactivate-message").empty();
                    jQuery("<span class='gs-valid-message'>Your account is removed. Reauthenticate again to integrate Elementor Form with Google Sheet.</span>").appendTo('#deactivate-message');
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            });
        } else {
            jQuery(".loading-sign-deactive").removeClass("loading");
        }



    });

    /**
    * Clear debug for system status tab
    */
   jQuery(document).on('click', '.clear-content-logs-elemnt', function () {

      jQuery(".clear-loading-sign-logs-elemnt").addClass("loading");
      var data = {
         action: 'elemnt_clear_debug_logs',
         security: jQuery('#gs-ajax-nonce-ele').val()
      };
      jQuery.post(ajaxurl, data, function ( response ) {
         if (response == -1) {
            return false; // Invalid nonce
         }
         
         if (response.success) {
            jQuery(".clear-loading-sign-logs-elemnt").removeClass("loading");
            jQuery('.clear-content-logs-msg-elemnt').html('Logs are cleared.');
            setTimeout(function () {
                        location.reload();
                    }, 1000);
         }
      });
   });

    /**
     * Clear debug for integration page
     */
    jQuery(document).on('click', '.debug-clear-elementor', function () {
        jQuery(".clear-loading-sign").addClass("loading");
        var data = {
            action: 'gs_clear_log_elementor',
            security: jQuery('#gs-ajax-nonce-ele').val()
        };
        jQuery.post(ajaxurl, data, function (response) {
             var clear_msg = response.data;
            if (response == -1) {
                return false; // Invalid nonce
            }

            if (response.success) {
                jQuery(".clear-loading-sign").removeClass("loading");
                jQuery("#gs-validation-message").empty();
                jQuery("<span class='gs-valid-message'>"+clear_msg+"</span>").appendTo('#gs-validation-message');
                setTimeout(function () {
                        location.reload();
                    }, 1000);
            }
        });
    });

    
	
	
	/**
     * Display Error logs
     */
	
	jQuery(document).ready(function($) {
      // Hide .wp-system-Error-logs initially
      $('.elemnt-system-Error-logs').hide();
  
      // Add a variable to track the state
      var isOpen = false;
  
      // Function to toggle visibility and button text
      function toggleLogs() {
          $('.elemnt-system-Error-logs').toggle();
          // Change button text based on visibility
          $('.elemnt-logs').text(isOpen ? 'View' : 'Close');
          isOpen = !isOpen; // Toggle the state
      }
  
      // Toggle visibility and button text when clicking .wpgsc-logs button
      $('.elemnt-logs').on('click', function() {
          toggleLogs();
      });
  
      // Prevent clicks inside the .elemnt-system-Error-logs div from closing it
      $('.elemnt-system-Error-logs').on('click', function(e) {
          e.stopPropagation(); // Prevents the div from closing when clicked inside
      });
  
      // Only close the .elemnt-system-Error-logs when the "Close" button is clicked
      $('.close-button').on('click', function() {
          $('.elemnt-system-Error-logs').hide();
          $('.elemnt-logs').text('View');
          isOpen = false;
      });
  });
	
	

    /**
     * Sync with google account to fetch latest sheet and tab name list.
     */
    jQuery(document).on('click', '#ele-sync', function () {
        jQuery(this).parent().children(".loading-sign").addClass("loading");
        var integration = jQuery(this).data("init");
        var data = {
            action: 'sync_google_account_ele',
            isajax: 'yes',
            isinit: integration,
            security: jQuery('#gs-ajax-nonce-ele').val()
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response == -1) {
                return false; // Invalid nonce
            }

            if (response.data.success === "yes") {
                jQuery(".loading-sign").removeClass("loading");
                jQuery("#gs-validation-message").empty();
                jQuery("<span class='gs-valid-message'>Fetched all sheet details.</span>").appendTo('#gs-validation-message');
            } else {
                jQuery(this).parent().children(".loading-sign").removeClass("loading");
                location.reload(); // simply reload the page
            }
        });
    });

    /** 
     * Get tab name list 
     */
    jQuery(document).on("change", ".elefgs_sheet_list select", function () {
        var sheetname = jQuery(this).val();
        console.log("I am here" . sheetname );
//        var sheetname = jQuery(this).val();
//
//        if (sheetname == "create_new") {
//            jQuery("#gs-sheet-tab-name").parent().hide();
//            jQuery("#gs-sheet-create-new").parent().show();
//            return;
//        } else {
//            jQuery("#gs-sheet-tab-name").parent().show();
//            jQuery("#gs-sheet-create-new").parent().hide();
//        }
//        jQuery(".loading-sign").addClass("loading");
//        var data = {
//            action: 'get_tab_list',
//            sheetname: sheetname,
//            security: jQuery('#gs-ajax-nonce-ele').val()
//        };
//
//        jQuery.post(ajaxurl, data, function (response) {
//            if (response == -1) {
//                return false; // Invalid nonce
//            }
//            if (response.success) {
//                jQuery('#gs-sheet-tab-name').html(html_decode(response.data));
//                jQuery(".loading-sign").removeClass("loading");
//            }
//        });
    });

    // TODO : Combine into one
    jQuery(document).on("change", "#gs-sheet-tab-name", function () {
        var tabname = jQuery(this).val();
        var sheetname = jQuery("#gs-sheet-name").val();
        jQuery(this).parent().children(".loading-sign").addClass("loading");
        var data = {
            action: 'get_sheet_id',
            tabname: tabname,
            sheetname: sheetname,
            security: jQuery('#gs-ajax-nonce-ele').val()
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response == -1) {
                return false; // Invalid nonce
            }

            if (response.success) {
                jQuery('#sheet-url').html(html_decode(response.data));
                jQuery(".loading-sign").removeClass("loading");
            }
        });
    });

    function html_decode(input) {
        var doc = new DOMParser().parseFromString(input, "text/html");
        return doc.documentElement.textContent;
    }

    // single checkbox with checked value get
    var count = jQuery("#drag").find("#total-count").val();
    jQuery("input[id='gs-cstm-chk']:checkbox").on("click", function () {
        if (jQuery(this).is(":checked")) {
            jQuery(this).closest("tr").find("td:eq(2)").each(function () {
                var txt = jQuery(this).closest("tr").find('td').find("input[type='text']").val();
                if (txt == "") {
                    var txt = jQuery(this).closest("tr").find('td').find("#custom-value").val();
                }
                jQuery("#drag").append("<div class='drag-item'><li class='draggable-item'>" + txt + "<input type='hidden' value='" + txt + "' id='gs-drag-drop' name='gs-drag-index[" + count + "]' > </li></div>");
            });
        } else {
            var getData = jQuery(this).closest("tr").find('td').find("#gs-custom-header").val();
            if (getData == "") {
                var getData = jQuery(this).closest("tr").find('td').find("#custom-value").val();
            }
            jQuery(".draggable-item").find("input[type='hidden']").each(function () {
                var getVal = jQuery(this).closest(".draggable-item").find('#gs-drag-drop').val();
                if (getData == getVal) {
                    var get = jQuery(this).closest(".drag-item").empty();
                }
            });
        }
        count++;
    });

    // jQuery(init);
    // function init() {
    //     jQuery(".droppable-area1").sortable({
    //         connectWith: ".connected-sortable",
    //         stack: '.connected-sortable ul',
    //         update: function () {
    //             var count = 0;
    //             jQuery.each(jQuery(".draggable-item input[id='gs-drag-drop']"), function () {
    //                 jQuery(this).attr("name", "gs-drag-index" + '[' + count + ']');
    //                 count++;
    //             });
    //         }
    //     }).disableSelection();
    // }

    // add input field for custom name
    jQuery(document).on("click", "#manual-name", function () {
        var sheetname = jQuery(this).val();
        jQuery(this).parent().children(".loading-sign").addClass("loading");
        if (jQuery(this).is(":checked")) {
            jQuery(".sheet-details").addClass('hide');
            jQuery(".manual-fields").removeClass('hide');
        } else {
            jQuery(".sheet-details").removeClass('hide');
            jQuery(".manual-fields").addClass('hide');
        }
    });


/**
     * On select wpform
     */
   jQuery('#metforms_select').change(function (e) {
      e.preventDefault();
      var FormId = jQuery(this).val();
      // alert(FormId);
      jQuery(".mf-loading-sign-select").addClass("mf-loading-select");
      jQuery.ajax({
         type: "POST",
         url: ajaxurl,
         dataType: "json",
         data: {
            action: 'get_gsc_metforms',
            metformsId: FormId,
            security: jQuery('#wp-ajax-nonce').val(),
         },
         cache: false,
         success: function (data) {          
            if (data['data_result'] == '') {
               return;
            }
            else {
               jQuery("#inside").empty();
               jQuery("#inside").append(html_decode(data.data));
               jQuery(".mf-loading-sign-select").removeClass("mf-loading-select");
            }
         }
      });
   });

});


jQuery(document).ready(function ($) {

    jQuery("#enable-sorting-option").change(function () {

        if (jQuery(this).is(":checked")) {
            jQuery(this).parents(".misc-options-row").find(".misc-options-inner").show();
        } else {
            jQuery(this).parents(".misc-options-row").find(".misc-options-inner").hide();
        }
    });

    jQuery("#enable-colors-option").change(function () {

        if (jQuery(this).is(":checked")) {
            jQuery(this).parents(".misc-options-row").find(".misc-options-inner").show();
        } else {
            jQuery(this).parents(".misc-options-row").find(".misc-options-inner").hide();
        }
    });

    if (jQuery(".inline-colors input").length > 0) {
        jQuery(".inline-colors input").wpColorPicker();
    }


    /**
      * verify the api code for manual setup
      * @since 1.0
      */
     jQuery(document).on('click', '#save-method-api-ele', function (event) {
         event.preventDefault();
         jQuery(".loading-sign-method-api").addClass("loading");
         var method_api_ele = jQuery("#ele_dro_option").val();
         console.log(method_api_ele);
         var data = {
             action: 'save_method_api_ele',
             method_api_ele : method_api_ele,
             security: jQuery('#gs-ajax-nonce-ele').val()
         };
         jQuery.post(ajaxurl, data, function (response) {
             setTimeout(function () {
                     location.reload();
                 }, 1000);
         });
     });

     /* drop down event for Google API */
   if(jQuery("#ele_manual_setting").val() == '1')
   {
      jQuery("#ele_dro_option").val('ele_manual');
      jQuery(".ele_api_manual_setting").show();
      jQuery(".ele_api_existing_setting").hide();
   }
   jQuery(document).on('change', '#ele_dro_option', function () {
          //alert(jQuery('option:selected', jQuery(this)).val());
          var option = jQuery('option:selected', jQuery(this)).val();
          if(option == "ele_manual")
          {
            jQuery(".ele_api_manual_setting").show();
            jQuery(".ele_api_existing_setting").hide();
          }else{
            jQuery(".ele_api_manual_setting").hide();
            jQuery(".ele_api_existing_setting").show();
          }  
   });
   /* drop down event for Google API */


    /**
   * verify the api code
   * @since 1.0
   */
   jQuery(document).on('click', '#ele-deactivate-auth', function (event) {
    console.log('==== here ==');
      event.preventDefault();
         jQuery( ".loading-sign" ).addClass( "loading" );
         var data = {
         action: 'ele_deactivate_auth_token_gapi',
         security: jQuery('#gs-ajax-nonce-ele').val()
         };
         jQuery.post(ajaxurl, data, function (response ) {
            if( ! response.success ) { 
               jQuery( ".loading-sign" ).removeClass( "loading" );
               jQuery( "#ele-validation-message" ).empty();
               jQuery("<span class='error-message'>Access code Can't be blank.</span>").appendTo('#ele-validation-message');
            } else {
               jQuery( ".loading-sign" ).removeClass( "loading" );
               jQuery( "#ele-validation-message" ).empty();
               jQuery("<span class='ele-valid-message'>Your account is removed. Reauthenticate again to integrate Elementor Form with Google Sheet.</span> ").appendTo('#ele-validation-message');
            setTimeout(function () { location.reload(); }, 1000);
           }
         });
         
   });



     /**
   * reset form
    @since 1.0
   */
   jQuery(document).on('click', '#save-ele-reset', function (event) {
      jQuery("#ele-client-id").val('');
      jQuery("#ele-secret-id").val('');
      jQuery("#ele-client-token").val('');
      jQuery("#ele-client-id").removeAttr('disabled');
      jQuery("#ele-secret-id").removeAttr('disabled');
      jQuery("#save-ele-manual").removeAttr('disabled');
      
   });

    jQuery(document).on('click', '.elementor-gs-sub-btn', function () {
        var feed_name = jQuery('.feedName').val();
        var elementorForms = jQuery('.elementorForms').val();

        if (feed_name != '' && elementorForms != '') {
            // Show loading indicator
            jQuery(".fld-fetch-load").addClass("loading");

            var data = {
                action: 'save_elementor_feed',
                security: jQuery('#elementorform-ajax-nonce').val(),
                feed_name: feed_name,
                elementorForms: elementorForms,
            };

            jQuery.post(ajaxurl, data, function (response) {
               
                if (response.success) {
                    jQuery(".fld-fetch-load").removeClass("loading");

                    if (response.data === 'Feed name already exists in the list, Please enter unique name of feed.') {
                        jQuery('.feed-error-message').html(response.data);
                        jQuery(".feed-error-message").show();
                        jQuery(".feed-success-message").hide();
                    } else if (response.data === 'Feed has been successfully created.') {
                        jQuery('.feed-success-message').html(response.data);
                        jQuery(".feed-error-message").hide();
                        jQuery(".feed-success-message").show();

                        // Update UI without page refresh
                        // You can optionally update the table or form here

                        // Example: Clear input fields
                        jQuery('.feedName').val('');
                        jQuery('.elementorForms').val('');

                       setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                }
            }, 'json');
        } else {
            alert('Please Enter Feed Name and Select Form.');
        }
    });

    jQuery(document).ready(function($) {
        $('.delete-feed').click(function() {
            var feedId = $(this).data('feed-id');
            var confirmDelete = confirm('Are you sure you want to delete this feed?');
            if (confirmDelete) {
              jQuery(".loading-sign-delete-feed-elegs" + feedId).addClass("loading");
                // Submit a POST request to delete the feed
                $.post(ajaxurl, {
                    action: 'delete_feed',
                    feed_id: feedId,
                    security: jQuery('#elementorform-ajax-nonce').val()
                }, function(response) {
                    if (response === 'success') {
                      jQuery(".loading-sign-delete-feed-elegs" + feedId).removeClass("loading");
                        // Reload the page after successful deletion
                        location.reload();
                    } else {
                        // Handle error here
                        console.log('Error deleting feed');
                    }
                });
            }
            else{
              jQuery(".loading-sign-delete-feed-elegs" + feedId).removeClass("loading");
            }
        });
    });



   /*Save ele integration(Auth) Manual */
   /**
   * verify the api code
   * @since 1.0
   */
   jQuery(document).on('click', '#save-ele-manual', function (event) {
      event.preventDefault();
      jQuery(".loading-sign").addClass("loading");
      var url = jQuery("#redirect_auth_eleforms").val();
      var data = {
         action: 'ele_save_client_id_sec_id_gapi',
         client_id: jQuery('#ele-client-id').val(),
         secret_id: jQuery('#ele-secret-id').val(),
         ele_client_token: jQuery('#ele-client-token').val(),
         security: jQuery('#gs-ajax-nonce-ele').val()
      };
      jQuery.post(ajaxurl, data, function (response) {
         if (!response.success) {
            jQuery(".loading-sign").removeClass("loading");
            jQuery("#ele-validation-message").empty();
            jQuery("<span class='error-message'>Access code Can't be blank.</span>").appendTo('#ele-validation-message');
         } else {
            jQuery(".loading-sign").removeClass("loading");
            jQuery("#ele-validation-message").empty();
            jQuery("<span class='ele-valid-message'>Your Google Access Code is Authorized and Saved.</span> ").appendTo('#ele-validation-message');
            //setTimeout(function () { location.reload(); }, 1000);
            setTimeout(function () {
               window.location.href = url;
            }, 1000);
         }
      });
   });

   if(jQuery("#fetchsheetDataEle").val() == 1){
        jQuery("#ele-sync").parent().find(".loading-sign").addClass("loading");
          var integration = jQuery("#ele-sync").data("init");
          var url = jQuery("#redirect_auth_eleforms").val();
          //url = url.slice( 1, url.indexOf('&') );
    
          var data = {
             action: 'sync_google_account_ele',
             isajax: 'yes',
             isinit: 'yes',
             security: jQuery('#gs-ajax-nonce-ele').val()
          };
          
          jQuery.post(ajaxurl, data, function (response) {

             if (response == -1) {
                return false; // Invalid nonce
             }

             if (response.data.success === "yes") {
                jQuery(".loading-sign").removeClass("loading");
                jQuery("#gsheet-validation-message").empty();
                jQuery("<span class='gsheet-valid-message'>Fetched all sheet details.</span>").appendTo('#gsheet-validation-message');

                setTimeout(function () {
                   window.location.href = url;
                }, 1000);
             } else {
                jQuery("#ele-sync").parent().find(".loading-sign").removeClass("loading");
                //location.reload(); // simply reload the page
                setTimeout(function () {
                   window.location.href = url;
                }, 1000);
             }
          });
       }




});


