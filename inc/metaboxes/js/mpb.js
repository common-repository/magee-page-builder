/**
 * Custom jQuery for Custom Metaboxes and Fields
 */
window.MPB = (function(window, document, $, undefined){
	'use strict';

	// localization strings
	var l10n = window.mpb_l10;
	var setTimeout = window.setTimeout;

	// MPB functionality object
	var mpb = {
		formfield   : '',
		idNumber    : false,
		file_frames : {},
		repeatEls   : 'input:not([type="button"]),select,textarea,.mpb_media_status'
	};

	mpb.metabox = function() {
		if ( mpb.$metabox ) {
			return mpb.$metabox;
		}
		mpb.$metabox = $('table.mpb_metabox');
		return mpb.$metabox;
	};

	mpb.init = function() {

		var $metabox = mpb.metabox();
		var $repeatGroup = $metabox.find('.repeatable-group');

		// hide our spinner gif if we're on a MP6 dashboard
		if ( l10n.new_admin_style ) {
			$metabox.find('.mpb-spinner img').hide();
		}

		/**
		 * Initialize time/date/color pickers
		 */
		mpb.initPickers( $metabox.find('input:text.mpb_timepicker'), $metabox.find('input:text.mpb_datepicker'), $metabox.find('input:text.mpb_colorpicker') );

		// Wrap date picker in class to narrow the scope of jQuery UI CSS and prevent conflicts
		$("#ui-datepicker-div").wrap('<div class="mpb_element" />');

		// Insert toggle button into DOM wherever there is multicheck. credit: Genesis Framework
		$( '<p><span class="button mpb-multicheck-toggle">' + l10n.check_toggle + '</span></p>' ).insertBefore( 'ul.mpb_checkbox_list' );

		$metabox
			.on( 'change', '.mpb_upload_file', function() {
				mpb.formfield = $(this).attr('id');
				$('#' + mpb.formfield + '_id').val('');
			})
			// Media/file management
			.on( 'click', '.mpb-multicheck-toggle', mpb.toggleCheckBoxes )
			.on( 'click', '.mpb_upload_button', mpb.handleMedia )
			.on( 'click', '.mpb_remove_file_button', mpb.handleRemoveMedia )
			// Repeatable content
			.on( 'click', '.add-group-row', mpb.addGroupRow )
			.on( 'click', '.add-row-button', mpb.addAjaxRow )
			.on( 'click', '.remove-group-row', mpb.removeGroupRow )
			.on( 'click', '.remove-row-button', mpb.removeAjaxRow )
			// Ajax oEmbed display
			.on( 'keyup paste focusout', '.mpb_oembed', mpb.maybeOembed )
			// Reset titles when removing a row
			.on( 'mpb_remove_row', '.repeatable-group', mpb.resetTitlesAndIterator );

		if ( $repeatGroup.length ) {
			$repeatGroup
				.filter('.sortable').each( function() {
					// Add sorting arrows
					$(this).find( '.remove-group-row' ).before( '<a class="shift-rows move-up alignleft" href="#">'+ l10n.up_arrow +'</a> <a class="shift-rows move-down alignleft" href="#">'+ l10n.down_arrow +'</a>' );
				})
				.on( 'click', '.shift-rows', mpb.shiftRows )
				.on( 'mpb_add_row', mpb.emptyValue );
		}

		// on pageload
		setTimeout( mpb.resizeoEmbeds, 500);
		// and on window resize
		$(window).on( 'resize', mpb.resizeoEmbeds );

	};

	mpb.resetTitlesAndIterator = function() {
		// Loop repeatable group tables
		$( '.repeatable-group' ).each( function() {
			var $table = $(this);
			// Loop repeatable group table rows
			$table.find( '.repeatable-grouping' ).each( function( rowindex ) {
				var $row = $(this);
				// Reset rows iterator
				$row.data( 'iterator', rowindex );
				// Reset rows title
				$row.find( '.mpb-group-title h4' ).text( $table.find( '.add-group-row' ).data( 'grouptitle' ).replace( '{#}', ( rowindex + 1 ) ) );
			});
		});
	};

	mpb.toggleCheckBoxes = function( event ) {
		event.preventDefault();
		var $self = $(this);
		var $multicheck = $self.parents( 'td' ).find( 'input[type=checkbox]' );

		// If the button has already been clicked once...
		if ( $self.data( 'checked' ) ) {
			// clear the checkboxes and remove the flag
			$multicheck.prop( 'checked', false );
			$self.data( 'checked', false );
		}
		// Otherwise mark the checkboxes and add a flag
		else {
			$multicheck.prop( 'checked', true );
			$self.data( 'checked', true );
		}
	};

	mpb.handleMedia = function(event) {

		if ( ! wp ) {
			return;
		}

		event.preventDefault();

		var $metabox     = mpb.metabox();
		var $self        = $(this);
		mpb.formfield    = $self.prev('input').attr('id');
		var $formfield   = $('#'+mpb.formfield);
		var formName     = $formfield.attr('name');
		var uploadStatus = true;
		var attachment   = true;
		var isList       = $self.hasClass( 'mpb_upload_list' );

		// If this field's media frame already exists, reopen it.
		if ( mpb.formfield in mpb.file_frames ) {
			mpb.file_frames[mpb.formfield].open();
			return;
		}

		// Create the media frame.
		mpb.file_frames[mpb.formfield] = wp.media.frames.file_frame = wp.media({
			title: $metabox.find('label[for=' + mpb.formfield + ']').text(),
			button: {
				text: l10n.upload_file
			},
			multiple: isList ? true : false
		});

		var handlers = {
			list : function( selection ) {
				// Get all of our selected files
				attachment = selection.toJSON();

				$formfield.val(attachment.url);
				$('#'+ mpb.formfield +'_id').val(attachment.id);

				// Setup our fileGroup array
				var fileGroup = [];

				// Loop through each attachment
				$( attachment ).each( function() {
					if ( this.type && this.type === 'image' ) {
						// image preview
						uploadStatus = '<li class="img_status">'+
							'<img width="50" height="50" src="' + this.url + '" class="attachment-50x50" alt="'+ this.filename +'">'+
							'<p><a href="#" class="mpb_remove_file_button" rel="'+ mpb.formfield +'['+ this.id +']">'+ l10n.remove_image +'</a></p>'+
							'<input type="hidden" id="filelist-'+ this.id +'" name="'+ formName +'['+ this.id +']" value="' + this.url + '">'+
						'</li>';

					} else {
						// Standard generic output if it's not an image.
						uploadStatus = '<li>'+ l10n.file +' <strong>'+ this.filename +'</strong>&nbsp;&nbsp;&nbsp; (<a href="' + this.url + '" target="_blank" rel="external">'+ l10n.download +'</a> / <a href="#" class="mpb_remove_file_button" rel="'+ mpb.formfield +'['+ this.id +']">'+ l10n.remove_file +'</a>)'+
							'<input type="hidden" id="filelist-'+ this.id +'" name="'+ formName +'['+ this.id +']" value="' + this.url + '">'+
						'</li>';

					}

					// Add our file to our fileGroup array
					fileGroup.push( uploadStatus );
				});

				// Append each item from our fileGroup array to .mpb_media_status
				$( fileGroup ).each( function() {
					$formfield.siblings('.mpb_media_status').slideDown().append(this);
				});
			},
			single : function( selection ) {
				// Only get one file from the uploader
				attachment = selection.first().toJSON();

				$formfield.val(attachment.url);
				$('#'+ mpb.formfield +'_id').val(attachment.id);

				if ( attachment.type && attachment.type === 'image' ) {
					// image preview
					uploadStatus = '<div class="img_status"><img style="max-width: 350px; width: 100%; height: auto;" src="' + attachment.url + '" alt="'+ attachment.filename +'" title="'+ attachment.filename +'" /><p><a href="#" class="mpb_remove_file_button" rel="' + mpb.formfield + '">'+ l10n.remove_image +'</a></p></div>';
				} else {
					// Standard generic output if it's not an image.
					uploadStatus = l10n.file +' <strong>'+ attachment.filename +'</strong>&nbsp;&nbsp;&nbsp; (<a href="'+ attachment.url +'" target="_blank" rel="external">'+ l10n.download +'</a> / <a href="#" class="mpb_remove_file_button" rel="'+ mpb.formfield +'">'+ l10n.remove_file +'</a>)';
				}

				// add/display our output
				$formfield.siblings('.mpb_media_status').slideDown().html(uploadStatus);
			}
		};

		// When an file is selected, run a callback.
		mpb.file_frames[mpb.formfield].on( 'select', function() {
			var selection = mpb.file_frames[mpb.formfield].state().get('selection');
			var type = isList ? 'list' : 'single';
			handlers[type]( selection );
		});

		// Finally, open the modal
		mpb.file_frames[mpb.formfield].open();
	};

	mpb.handleRemoveMedia = function( event ) {
		event.preventDefault();
		var $self = $(this);
		if ( $self.is( '.attach_list .mpb_remove_file_button' ) ){
			$self.parents('li').remove();
			return false;
		}
		mpb.formfield    = $self.attr('rel');
		var $container   = $self.parents('.img_status');

		mpb.metabox().find('input#' + mpb.formfield).val('');
		mpb.metabox().find('input#' + mpb.formfield + '_id').val('');
		if ( ! $container.length ) {
			$self.parents('.mpb_media_status').html('');
		} else {
			$container.html('');
		}
		return false;
	};

	// src: http://www.benalman.com/projects/jquery-replacetext-plugin/
	$.fn.replaceText = function(b, a, c) {
		return this.each(function() {
			var f = this.firstChild, g, e, d = [];
			if (f) {
				do {
					if (f.nodeType === 3) {
						g = f.nodeValue;
						e = g.replace(b, a);
						if (e !== g) {
							if (!c && /</.test(e)) {
								$(f).before(e);
								d.push(f);
							} else {
								f.nodeValue = e;
							}
						}
					}
				} while (f = f.nextSibling);
			}
			if ( d.length ) { $(d).remove(); }
		});
	};

	$.fn.cleanRow = function( prevNum, group ) {
		var $self = $(this);
		var $inputs = $self.find('input:not([type="button"]), select, textarea, label');
		if ( group ) {
			// Remove extra ajaxed rows
			$self.find('.mpb-repeat-table .repeat-row:not(:first-child)').remove();
		}
		mpb.$focus = false;
		mpb.neweditor_id = [];

		$inputs.filter(':checked').removeAttr( 'checked' );
		$inputs.filter(':selected').removeAttr( 'selected' );

		if ( $self.find('.mpb-group-title') ) {
			$self.find( '.mpb-group-title h4' ).text( $self.data( 'title' ).replace( '{#}', ( mpb.idNumber + 1 ) ) );
		}

		$inputs.each( function(){
			var $newInput = $(this);
			var isEditor  = $newInput.hasClass( 'wp-editor-area' );
			var oldFor    = $newInput.attr( 'for' );
			// var $next     = $newInput.next();
			var attrs     = {};
			var newID, oldID;
			if ( oldFor ) {
				attrs = { 'for' : oldFor.replace( '_'+ prevNum, '_'+ mpb.idNumber ) };
			} else {
				var oldName = $newInput.attr( 'name' );
				// Replace 'name' attribute key
				var newName = oldName ? oldName.replace( '['+ prevNum +']', '['+ mpb.idNumber +']' ) : '';
				oldID       = $newInput.attr( 'id' );
				newID       = oldID ? oldID.replace( '_'+ prevNum, '_'+ mpb.idNumber ) : '';
				attrs       = {
					id: newID,
					name: newName,
					// value: '',
					'data-iterator': mpb.idNumber,
				};
			}

			$newInput
				.removeClass( 'hasDatepicker' )
				.attr( attrs ).val('');

			// wysiwyg field
			if ( isEditor ) {
				// Get new wysiwyg ID
				newID = newID ? oldID.replace( 'zx'+ prevNum, 'zx'+ mpb.idNumber ) : '';
				// Empty the contents
				$newInput.html('');
				// Get wysiwyg field
				var $wysiwyg = $newInput.parents( '.mpb-type-wysiwyg' );
				// Remove extra mce divs
				$wysiwyg.find('.mce-tinymce:not(:first-child)').remove();
				// Replace id instances
				var html = $wysiwyg.html().replace( new RegExp( oldID, 'g' ), newID );
				// Update field html
				$wysiwyg.html( html );
				// Save ids for later to re-init tinymce
				mpb.neweditor_id.push( { 'id': newID, 'old': oldID } );
			}

			mpb.$focus = mpb.$focus ? mpb.$focus : $newInput;
		});

		return this;
	};

	$.fn.newRowHousekeeping = function() {
		var $row         = $(this);
		var $colorPicker = $row.find( '.wp-picker-container' );
		var $list        = $row.find( '.mpb_media_status' );

		if ( $colorPicker.length ) {
			// Need to clean-up colorpicker before appending
			$colorPicker.each( function() {
				var $td = $(this).parent();
				$td.html( $td.find( 'input:text.mpb_colorpicker' ).attr('style', '') );
			});
		}

		// Need to clean-up colorpicker before appending
		if ( $list.length ) {
			$list.empty();
		}

		return this;
	};

	mpb.afterRowInsert = function( $row ) {
		if ( mpb.$focus ) {
			mpb.$focus.focus();
		}

		var _prop;

		// Need to re-init wp_editor instances
		if ( mpb.neweditor_id.length ) {
			var i;
			for ( i = mpb.neweditor_id.length - 1; i >= 0; i-- ) {
				var id = mpb.neweditor_id[i].id;
				var old = mpb.neweditor_id[i].old;

				if ( typeof( tinyMCEPreInit.mceInit[ id ] ) === 'undefined' ) {
					var newSettings = jQuery.extend( {}, tinyMCEPreInit.mceInit[ old ] );

					for ( _prop in newSettings ) {
						if ( 'string' === typeof( newSettings[_prop] ) ) {
							newSettings[_prop] = newSettings[_prop].replace( new RegExp( old, 'g' ), id );
						}
					}
					tinyMCEPreInit.mceInit[ id ] = newSettings;
				}
				if ( typeof( tinyMCEPreInit.qtInit[ id ] ) === 'undefined' ) {
					var newQTS = jQuery.extend( {}, tinyMCEPreInit.qtInit[ old ] );
					for ( _prop in newQTS ) {
						if ( 'string' === typeof( newQTS[_prop] ) ) {
							newQTS[_prop] = newQTS[_prop].replace( new RegExp( old, 'g' ), id );
						}
					}
					tinyMCEPreInit.qtInit[ id ] = newQTS;
				}
				tinyMCE.init({
					id : tinyMCEPreInit.mceInit[ id ],
				});

			}
		}

		// Init pickers from new row
		mpb.initPickers( $row.find('input:text.mpb_timepicker'), $row.find('input:text.mpb_datepicker'), $row.find('input:text.mpb_colorpicker') );
	};

	mpb.updateNameAttr = function () {

		var $this = $(this);
		var name  = $this.attr( 'name' ); // get current name

		// No name? bail
		if ( typeof name === 'undefined' ) {
			return false;
		}

		var prevNum = parseInt( $this.parents( '.repeatable-grouping' ).data( 'iterator' ) );
		var newNum  = prevNum - 1; // Subtract 1 to get new iterator number

		// Update field name attributes so data is not orphaned when a row is removed and post is saved
		var $newName = name.replace( '[' + prevNum + ']', '[' + newNum + ']' );

		// New name with replaced iterator
		$this.attr( 'name', $newName );

	};

	mpb.emptyValue = function( event, row ) {
		$('input:not([type="button"]), textarea', row).val('');
	};

	mpb.addGroupRow = function( event ) {

		event.preventDefault();

		var $self    = $(this);
		var $table   = $('#'+ $self.data('selector'));
		var $oldRow  = $table.find('.repeatable-grouping').last();
		var prevNum  = parseInt( $oldRow.data('iterator') );
		mpb.idNumber = prevNum + 1;
		var $row     = $oldRow.clone();

		$row.data( 'title', $self.data( 'grouptitle' ) ).newRowHousekeeping().cleanRow( prevNum, true );

		// console.log( '$row.html()', $row.html() );
		var $newRow = $( '<tr class="repeatable-grouping" data-iterator="'+ mpb.idNumber +'">'+ $row.html() +'</tr>' );
		$oldRow.after( $newRow );
		// console.log( '$newRow.html()', $row.html() );

		mpb.afterRowInsert( $newRow );

		if ( $table.find('.repeatable-grouping').length <= 1 ) {
			$table.find('.remove-group-row').prop('disabled', true);
		} else {
			$table.find('.remove-group-row').removeAttr( 'disabled' );
		}

		$table.trigger( 'mpb_add_row', $newRow );
		
		$('.icp-auto').iconpicker();
					
	};

	mpb.addAjaxRow = function( event ) {

		event.preventDefault();

		var $self         = $(this);
		var tableselector = '#'+ $self.data('selector');
		var $table        = $(tableselector);
		var $emptyrow     = $table.find('.empty-row');
		var prevNum       = parseInt( $emptyrow.find('[data-iterator]').data('iterator') );
		mpb.idNumber      = prevNum + 1;
		var $row          = $emptyrow.clone();

		$row.newRowHousekeeping().cleanRow( prevNum );

		$emptyrow.removeClass('empty-row').addClass('repeat-row');
		$emptyrow.after( $row );

		mpb.afterRowInsert( $row );
		$table.trigger( 'mpb_add_row', $row );
	};

	mpb.removeGroupRow = function( event ) {
		event.preventDefault();
		var $self   = $(this);
		var $table  = $('#'+ $self.data('selector'));
		var $parent = $self.parents('.repeatable-grouping');
		var noRows  = $table.find('.repeatable-grouping').length;

		// when a group is removed loop through all next groups and update fields names
		$parent.nextAll( '.repeatable-grouping' ).find( mpb.repeatEls ).each( mpb.updateNameAttr );

		if ( noRows > 1 ) {
			$parent.remove();
			if ( noRows < 3 ) {
				$table.find('.remove-group-row').prop('disabled', true);
			} else {
				$table.find('.remove-group-row').prop('disabled', false);
			}
			$table.trigger( 'mpb_remove_row' );
		}
	};

	mpb.removeAjaxRow = function( event ) {
		event.preventDefault();
		var $self   = $(this);
		var $parent = $self.parents('tr');
		var $table  = $self.parents('.mpb-repeat-table');

		// mpb.log( 'number of tbodys', $table.length );
		// mpb.log( 'number of trs', $('tr', $table).length );
		if ( $table.find('tr').length > 1 ) {
			if ( $parent.hasClass('empty-row') ) {
				$parent.prev().addClass( 'empty-row' ).removeClass('repeat-row');
			}
			$self.parents('.mpb-repeat-table tr').remove();
			$table.trigger( 'mpb_remove_row' );
		}
	};

	mpb.shiftRows = function( event ) {

		event.preventDefault();

		var $self     = $(this);
		var $parent   = $self.parents( '.repeatable-grouping' );
		var $goto     = $self.hasClass( 'move-up' ) ? $parent.prev( '.repeatable-grouping' ) : $parent.next( '.repeatable-grouping' );

		if ( ! $goto.length ) {
			return;
		}

		var inputVals = [];
		// Loop this items fields
		$parent.find( mpb.repeatEls ).each( function() {
			var $element = $(this);
			var val;
			if ( $element.hasClass('mpb_media_status') ) {
				// special case for image previews
				val = $element.html();
			} else if ( 'checkbox' === $element.attr('type') ) {
				val = $element.is(':checked');
				mpb.log( 'checked', val );
			} else if ( 'select' === $element.prop('tagName') ) {
				val = $element.is(':selected');
				mpb.log( 'checked', val );
			} else {
				val = $element.val();
			}
			// Get all the current values per element
			inputVals.push( { val: val, $: $element } );
		});
		// And swap them all
		$goto.find( mpb.repeatEls ).each( function( index ) {
			var $element = $(this);
			var val;

			if ( $element.hasClass('mpb_media_status') ) {
				// special case for image previews
				val = $element.html();
				$element.html( inputVals[ index ]['val'] );
				inputVals[ index ]['$'].html( val );

			}
			// handle checkbox swapping
			else if ( 'checkbox' === $element.attr('type') ) {
				inputVals[ index ]['$'].prop( 'checked', $element.is(':checked') );
				$element.prop( 'checked', inputVals[ index ]['val'] );
			}
			// handle select swapping
			else if ( 'select' === $element.prop('tagName') ) {
				inputVals[ index ]['$'].prop( 'selected', $element.is(':selected') );
				$element.prop( 'selected', inputVals[ index ]['val'] );
			}
			// handle normal input swapping
			else {
				inputVals[ index ]['$'].val( $element.val() );
				$element.val( inputVals[ index ]['val'] );
			}
		});
	};

	/**
	 * @todo make work, always
	 */
	mpb.initPickers = function( $timePickers, $datePickers, $colorPickers ) {
		// Initialize timepicker
		mpb.initTimePickers( $timePickers );

		// Initialize jQuery UI datepicker
		mpb.initDatePickers( $datePickers );

		// Initialize color picker
		mpb.initColorPickers( $colorPickers );
	};

	mpb.initTimePickers = function( $selector ) {
		if ( ! $selector.length ) {
			return;
		}

		$selector.timePicker({
			startTime: "00:00",
			endTime: "23:59",
			show24Hours: false,
			separator: ':',
			step: 30
		});
	};

	mpb.initDatePickers = function( $selector ) {
		if ( ! $selector.length ) {
			return;
		}

		$selector.datepicker( "destroy" );
		$selector.datepicker();
	};

	mpb.initColorPickers = function( $selector ) {
		if ( ! $selector.length ) {
			return;
		}
		if (typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function') {

			$selector.wpColorPicker();

		} else {
			$selector.each( function(i) {
				$(this).after('<div id="picker-' + i + '" style="z-index: 1000; background: #EEE; border: 1px solid #CCC; position: absolute; display: block;"></div>');
				$('#picker-' + i).hide().farbtastic($(this));
			})
			.focus( function() {
				$(this).next().show();
			})
			.blur( function() {
				$(this).next().hide();
			});
		}
	};

	mpb.maybeOembed = function( evt ) {
		var $self = $(this);
		var type = evt.type;

		var m = {
			focusout : function() {
				setTimeout( function() {
					// if it's been 2 seconds, hide our spinner
					mpb.spinner( '.postbox table.mpb_metabox', true );
				}, 2000);
			},
			keyup : function() {
				var betw = function( min, max ) {
					return ( evt.which <= max && evt.which >= min );
				};
				// Only Ajax on normal keystrokes
				if ( betw( 48, 90 ) || betw( 96, 111 ) || betw( 8, 9 ) || evt.which === 187 || evt.which === 190 ) {
					// fire our ajax function
					mpb.doAjax( $self, evt);
				}
			},
			paste : function() {
				// paste event is fired before the value is filled, so wait a bit
				setTimeout( function() { mpb.doAjax( $self ); }, 100);
			}
		};
		m[type]();

	};

	/**
	 * Resize oEmbed videos to fit in their respective metaboxes
	 */
	mpb.resizeoEmbeds = function() {
		mpb.metabox().each( function() {
			var $self      = $(this);
			var $tableWrap = $self.parents('.inside');
			if ( ! $tableWrap.length )  {
				return true; // continue
			}

			// Calculate new width
			var newWidth = Math.round(($tableWrap.width() * 0.82)*0.97) - 30;
			if ( newWidth > 639 ) {
				return true; // continue
			}

			var $embeds   = $self.find('.mpb-type-oembed .embed_status');
			var $children = $embeds.children().not('.mpb_remove_wrapper');
			if ( ! $children.length ) {
				return true; // continue
			}

			$children.each( function() {
				var $self     = $(this);
				var iwidth    = $self.width();
				var iheight   = $self.height();
				var _newWidth = newWidth;
				if ( $self.parents( '.repeat-row' ).length ) {
					// Make room for our repeatable "remove" button column
					_newWidth = newWidth - 91;
				}
				// Calc new height
				var newHeight = Math.round((_newWidth * iheight)/iwidth);
				$self.width(_newWidth).height(newHeight);
			});

		});
	};

	/**
	 * Safely log things if query var is set
	 
	 */
	mpb.log = function() {
		if ( l10n.script_debug && console && typeof console.log === 'function' ) {
			console.log.apply(console, arguments);
		}
	};

	mpb.spinner = function( $context, hide ) {
		if ( hide ) {
			$('.mpb-spinner', $context ).hide();
		}
		else {
			$('.mpb-spinner', $context ).show();
		}
	};

	// function for running our ajax
	mpb.doAjax = function($obj) {
		// get typed value
		var oembed_url = $obj.val();
		// only proceed if the field contains more than 6 characters
		if ( oembed_url.length < 6 ) {
			return;
		}

		// only proceed if the user has pasted, pressed a number, letter, or whitelisted characters

			// get field id
			var field_id = $obj.attr('id');
			// get our inputs $context for pinpointing
			var $context = $obj.parents('.mpb-repeat-table  tr td');
			$context = $context.length ? $context : $obj.parents('.mpb_metabox tr td');

			var embed_container = $('.embed_status', $context);
			var oembed_width = $obj.width();
			var child_el = $(':first-child', embed_container);

			// http://www.youtube.com/watch?v=dGG7aru2S6U
			mpb.log( 'oembed_url', oembed_url, field_id );
			oembed_width = ( embed_container.length && child_el.length ) ? child_el.width() : $obj.width();

			// show our spinner
			mpb.spinner( $context );
			// clear out previous results
			$('.embed_wrap', $context).html('');
			// and run our ajax function
			setTimeout( function() {
				// if they haven't typed in 500 ms
				if ( $('.mpb_oembed:focus').val() !== oembed_url ) {
					return;
				}
				$.ajax({
					type : 'post',
					dataType : 'json',
					url : l10n.ajaxurl,
					data : {
						'action': 'mpb_oembed_handler',
						'oembed_url': oembed_url,
						'oembed_width': oembed_width > 300 ? oembed_width : 300,
						'field_id': field_id,
						'object_id': $obj.data('objectid'),
						'object_type': $obj.data('objecttype'),
						'mpb_ajax_nonce': l10n.ajax_nonce
					},
					success: function(response) {
						mpb.log( response );
						// Make sure we have a response id
						if ( typeof response.id === 'undefined' ) {
							return;
						}

						// hide our spinner
						mpb.spinner( $context, true );
						// and populate our results from ajax response
						$('.embed_wrap', $context).html(response.result);
					}
				});

			}, 500);
	};

	$(document).ready(mpb.init);

	return mpb;

})(window, document, jQuery);
