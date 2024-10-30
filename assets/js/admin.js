jQuery(document).ready(function($){
	
	
	 $(document).on('click','.mpb-post-icon-picker', function() {
                    $('.icp-auto').iconpicker();
                    $('.icp-dd').iconpicker({});
                    
                    $('.icp-glyphs').iconpicker({
                        title: 'Prepending glypghicons',
                        icons: $.merge(['glyphicon-home', 'glyphicon-repeat', 'glyphicon-search',
                            'glyphicon-arrow-left', 'glyphicon-arrow-right', 'glyphicon-star'], $.iconpicker.defaultOptions.icons),
                        fullClassFormatter: function(val){
                            if(val.match(/^fa-/)){
                                return 'fa '+val;
                            }else{
                                return 'glyphicon '+val;
                            }
                        }
                    });
                  
                }).trigger('click');

 $('.iconpicker-container').each(function(){
		var icon = $(this).find('.input-group-addon').html();
		var fa = $(this).find('input').val();
		if(!icon)
		$(this).find('.input-group-addon').html('<i class="fa '+fa+'"></i>');
      });
 });