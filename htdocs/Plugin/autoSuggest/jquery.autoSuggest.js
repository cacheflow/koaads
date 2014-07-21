
/* DOCUMENTATION:
   Use Example: $('input').suggestive({ backend : '', mod: '', limit: ''})
   PURPOSE: Will use JSON encoded array created from found entries to insert into input via jQuery UI autocomplete
   NOTES: callback=? is to prevent CSRF as it creates a unique hash value and autocomplete ensures integrity
        
          Out of all the variables, the only one that's needed is a url to backend which gets results
          and encodes them in the format $_GET['callback'] . "(" . json_encode($resultset) . ")"; and echos
          (or via another method) so autocomplete can use it
 
 +=====================================================+   
 | Dynamically Set (options to pass to Statically Set) |
 +=====================================================+ 
 * @backend(url)::str - points to the script that will connect to database to get the list of "entries"  
 * @mod::all types - to make request more specific 
 * @limit::int - to limit the request amount

 +==========================================+
 |  Statically Set (referenced with $_POST) |
 +==========================================+
 *  ajax = 1
 *  mode = settings.mod
 *  term = req.term
       This is your user input from the input element more in JQUI Autocomplete DOCs
       http://jqueryui.com/demos/autocomplete/ ***under third variation of Overview***
 *  results = settings.limit  
 
 +=========+
 | Example |
 +=========+
 
 *if($_POST[ajax])
 * switch ($_POST['mode']) ++++++++differentiating different calls++++++++
 * case 'location':  
 
   $locations = $this->modelCache['location']->getLocations('DISTINCT city,state', "WHERE city REGEXP '^({$_POST['term']})' LIMIT {$_POST['results']}");
    
    for($i = 0; $i < count($locations); $i++)  ++++++++Glues city, state e.g. los angeles, CA for each row++++++++ 
        $locations[$i] = implode(',', $locations[$i]);
    
    if($locations == null)    ++++++++sets location to array b/c js will complain if it tries to parse nothing++++++++
        $locations = array();
    echo  $_GET['callback'] . "(" . json_encode($locations) . ")";
 */

(function($){
    $.fn.autoSuggest = function(options){
        //private attributes
        var settings = $.extend({
           'backend' : 'index.php?process=home',
           'ajax' : 'location_autoComplete',
           'limit' : 10
        }, options);
        
        var urlPattern = /\?$/;
        
        return this.each(function(){
            $.ajaxSetup({
                cache: false
            });
            
            $(this).autocomplete({
                source : function(req, add){
                    $.ajax( settings.backend + (!urlPattern.test(settings.backend) ? '&' : '' ) + 'callback=?', {
                        type: 'POST',
                        dataType: 'jsonp',
                        data: {
                            ajax : settings.ajax,
                            input : req.term,
                            limit : settings.limit
                        },
                        success: function(data){
                            var suggestions = [];
                            $.each(data, function(i, val){
                                suggestions.push(val);
                            });
                            add(suggestions);
                        }
                    });
                }
            });
        });
    };

})(jQuery);