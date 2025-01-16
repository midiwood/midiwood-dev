jQuery(window).on(
        'load',
        function () {
            elementor.channels.editor.on(
                    'namespace:editor:gsceviewsheet',
                    function (view) {
                        var value = jQuery("[data-setting = gs_spreadsheet_id]").val();
                        var tabid = jQuery("[data-setting = gs_spreadsheet_tab_name] option:selected").val();

                        if (String(value) === '' || String(value) === 'undefined' || String(value) === 'null') {
                            alert('Please Select Spreadsheet');
                            return false;
                        }

                        if (String(value) === 'new') {
                            alert('Spreadsheet is not yet created');
                            return false;
                        }

                        var URL = 'https://docs.google.com/spreadsheets/d/' + value +'/edit#gid='+tabid;
                        window.open(URL, "_blank");
                    }
            );
        }

);



jQuery(window).on(
        'load',
        function () {
            elementor.channels.editor.on(
                    'namespace:editor:gscfetchsheet',
                    function (view) {
                        //console.log("=========== click here to fetch spreadsheet ==========");

                        jQuery(".elementor-button-gscfetchsheet").text("Loading..................");

                        var data = {
                            action: 'sync_google_account_ele_elementor',
                            isajax: 'yes',
                            isinit: 'yes',
                            security: jQuery('[data-setting = gs-ajax-nonce-ele]').val()
                        };
                        jQuery.post(ajaxurl, data, function (response) {
                            if (response == -1) {
                               return false; // Invalid nonce
                            }
                   
                            if (response.data.success === "yes") {
                               jQuery(".elementor-button-gscfetchsheet").text(" Successfully fetched! ");
                               setTimeout(function () {
                                  location.reload();
                               }, 1000);
                            } else {
                               jQuery(".elementor-button-gscfetchsheet").text("Something wrong! ");
                               setTimeout(function () {
                                  location.reload();
                               }, 1000);
                            }
                        });
                    }
            );
        }

);


jQuery(window).on(
        'load',
        function () {
            elementor.channels.editor.on(
                    'change',
                    function (view) {
                        var changed = view.elementSettingsModel.changed;
                        if (changed.gs_spreadsheet_id != "" && changed.gs_spreadsheet_id != undefined) {
                            var sheetnameId = jQuery("[data-setting = gs_spreadsheet_id]").val();
                            var tabs_arr_val = jQuery("[data-setting = gs_elmentor_all_sheet_data]").val();
                            tabs_arr_val = jQuery.parseJSON(tabs_arr_val);
                            if (tabs_arr_val[sheetnameId] == undefined || tabs_arr_val == "") {
                              fetch_tabs_api(sheetnameId, 0);
                            } else {
                              fetch_tabs_api(sheetnameId, 0);
                               //options_selected_sheet(sheetnameId);
                            }
                         }

                    }
            );
        }
);


function fetch_tabs_api(sheetnameId, refresh) {
    jQuery(".loading-gs-fetch-tabs").addClass("loading");
   jQuery("[data-setting =  gs_spreadsheet_tab_name]").attr('disabled', true);
   jQuery(".tabselectionloading").css('display','inline-block');


    //jQuery("#gsheet_tabs_arr").val('');
    var refresh = 0;
    var data = {
       action: "get_google_tab_list_by_sheetname",
       sheetname: sheetnameId,
       refresh: refresh,
       security: jQuery("[data-setting = gs-ajax-nonce-ele]").val(),
    };
 
    jQuery.post(ajaxurl, data, function (response) {
       if (response == -1) {
          return false; // Invalid nonce
       }
       if (response) {
          jQuery("[data-setting = gs_elmentor_all_sheet_data]").val("");
          jQuery("[data-setting = gs_elmentor_all_sheet_data]").val(response);
          options_selected_sheet(sheetnameId);
         jQuery("[data-setting =  gs_spreadsheet_tab_name]").attr('disabled', false);
         jQuery(".tabselectionloading").css('display','none');
       }
    });
 }
 
 function options_selected_sheet(sheetnameId) {
    //var tabs_arr_val = jQuery("[data-setting = gs_elmentor_all_sheet_data]").val();
    var all_tab_array = jQuery("[data-setting = gs_elmentor_all_sheet_data]").val();
    all_tab_array = jQuery.parseJSON(all_tab_array);
 
    var tabs_arr = all_tab_array[sheetnameId];
    //console.log(tabs_arr);
    if (all_tab_array != undefined || all_tab_array != "") {
       var tabs_option = "";
       jQuery.each(tabs_arr, function (key, value) {
          tabs_option += '<option value="' + key + '">' + value + "</option>";
       });
       if ((tabs_option != "") | (tabs_option != undefined)) {
          jQuery("[data-setting =  gs_spreadsheet_tab_name]").html(tabs_option);
          jQuery("[data-setting =  gs_spreadsheet_tab_name]").trigger("change");
          var tabname = jQuery("[data-setting = gs_spreadsheet_tab_name] option:selected").text();
       }
    }
 }