(function($){

	$(window).load(function(){
		var i = 0;
		//js code for admin
		if($('#content').length > 0){
			var editor_ex = $('#content'),
			//nascondo post_content
				editor = $('.postarea.wp-editor-expand').hide(),
				new_editor = editor.after('<div class="postbox jsonData new-editor"><h3>Gestione Modelli Json</h3><div class="inside"></div></div>').next(),
			//add fixed opt
				list_opt = [{label:'Detail cut:',ref:'detail-cut'},{label:'Detail:',ref:'detail'}],
				opt = $('<div><label></label> <input type="text" class="regular-text" ref=""></div>');



			//add fn
			new_editor.prepend('<small>* Aggiungere il campo personalizzato "campi dettaglio modelli" per preimpostare i dati da inserire nei modelli,dividere i nomi campo con ";"</small>');
			var btn = $('<input type="button" value="Nuovo Modello" class="button" >').bind('click',function(){
				//pre save data for autosaved
				Model.init ($(new_editor).children('.inside').children('ul'));
			});
			// creazione nuovo editor
			new_editor.children('.inside').append(btn).append('<ul></ul>');



			var Model = new function(){
				this.self = this;
				this.getFields = function(){
					return $('#list-table>#the-list').find('input[value=campi_dettaglio_modelli]').parent().next().children('textarea').val();
				};

				this.init = function(content,v){
					var el = $('<li><label><b>Model Name:</b></label> <input type="text" class="regular-text" ref="model-name"><ul></ul><div class="close">X</div></li>');
					list_fields = this.self.getFields();
					//button close
					el.children('div.close').bind('click',function(){
						if( confirm('Eliminare il modello?') )
							$(this).parent().remove();
					});
					
					
					if ( list_fields.length <= 0 ){
						alert('Attenzione, compilare la custom field "campi_dettaglio_modelli" con le intestazioni necessarie');
						return;
					}
						
					$.each(list_fields.split(';'),function(k,v){ 
						if(v.length > 0){
							var name_field = (v.trim().replace(/[^a-z0-9]/gi,'_').toLowerCase());
							var el_field = $('<li><label>'+v+':</label> <input type="text" class="regular-text" ref="'+name_field+'"></li>');
							el.children('ul').append(el_field);
						}
					});

					// create element
					$(content).append(el);
					
					//load data
					if(v && !$.isEmptyObject(v)){ 
						
						el.children('input').val(v.model);
						//console.log(values);
						$.each(v.list,function(k,va){
							el.children('ul').find('input[ref='+k+']').val(va);
						});
						
					}

				}; // /objModel

				this.saveInEditor = function(textarea){
					var val = {};
					//div for block
					$(new_editor).find('.inside>ul>li').each(function(){
						var modelName = $(this).children('input:first').val();
						val[modelName] = {list:{},opt:{}};
						$(this).children('ul').find('input').each(function(){
							val[modelName]['list'][$(this).attr('ref')] = $(this).val();
						});


						val[modelName]['opt']['detail'] = $(this).children('div:eq(0)').children('input').val();
						val[modelName]['opt']['detail-cut'] =  $(this).children('div:eq(1)').children('input').val();
					});
					
					//clear textarea shortcode
					textarea.val(textarea.val().replace(/\[jsondata\](.*)\[\/jsondata\]/,''));

					if(!$.isEmptyObject(val)){
						textarea.val(textarea.val() + ('[jsondata]' + JSON.stringify(val) + '[/jsondata]'));
					}
				}; //endsaveInEdior

			};// endclass

	

			// load data
			if(editor_ex.val().length > 0){
				$(new_editor).children('.inside').children('ul').append('<li class="wait"></li>');

				var val = '';
				if(editor_ex.val().indexOf('[jsondata]') != -1){
					var tab_content = editor_ex.val().match(/\[jsondata\](.*)\[\/jsondata\]/);
					val = JSON.parse(tab_content[1]);

					if(!$.isEmptyObject(val)){ 
						$.each(val,function(k,v){ 
							v['model'] = k;
							Model.init($(new_editor).children('.inside').children('ul'),v);
						});
					}

					$(new_editor).children('.inside').find('li.wait').remove();
				}
			}
			// 
			
			// salvataggio
			editor.parents('form:first').bind('submit',function(){
				Model.saveInEditor(editor_ex);
				return true;
			});
		}//end if content

	});
})(jQuery);
