;( function( $ ) {
	$( function() {

		$( document.body )
			.on( 'init_tooltips', function() {
				var tiptip_args = {
					'attribute': 'data-tip',
					'fadeIn': 50,
					'fadeOut': 50,
					'delay': 200
				};

				$( '.tips, .help_tip, .woocommerce-help-tip' ).tipTip( tiptip_args );

				// Add tiptip to parent element for widefat tables
				$( '.parent-tips' ).each( function() {
					$( this ).closest( 'a, th' ).attr( 'data-tip', $( this ).data( 'tip' ) ).tipTip( tiptip_args ).css( 'cursor', 'help' );
				});
			})
			.trigger( 'init_tooltips' );

		function generate_order_PDFs_preview(payload, parent){
			parent.append($('<p style="position: relative; height: 95%; width: 100%;"></p>'));

			if( 1 === payload.length ) {
				parent.find('p').append($('<iframe  style="position: relative; height: 100%; width: 100%;" src="' + payload[0] + '"></iframe>'));
			}
			else if( payload.length ){
				let tabs= $('<nav class="nav-tab-wrapper woo-nav-tab-wrapper"></nav>');

				payload.forEach((v,i)=>{
					let tab = $('<a class="nav-tab pacsoft-order-pdf-tab"></a>');
					if(i===0){
						tab.addClass('nav-tab-active');
					}
					tab.html(i+1);
					tab.attr('data-iframe_id','pacsoft-order-pdf-'+i);

					tab.on('click', (evt)=>{
						let that = $(evt.target);
						$('a.nav-tab.pacsoft-order-pdf-tab').removeClass('nav-tab-active');
						that.addClass('nav-tab-active');
						$('iframe.pacsoft-order-pdf-iframe').hide();
						$('#'+that.attr('data-iframe_id')).show();
					});

					tabs.append(tab);


					let iframe = $('<iframe></iframe>');
					iframe.addClass('pacsoft-order-pdf-iframe');
					iframe.attr('src', v);
					iframe.attr('id', 'pacsoft-order-pdf-'+i);
					iframe.css('position','relative');
					iframe.css('height','95%');
					iframe.css('width','100%');
					if (i !== 0) {
						iframe.css('display', 'none');
					}

					parent.find('p').append(iframe);
				});
				// let merge_tab = $('<a class="nav-tab-wrapper woo-nav-tab-wrapper">Merge</a>');
				// // /wp-admin/edit.php?s=&post_status=all&post_type=shop_order&_wpnonce=36f2eb7676&_wp_http_referer=%2Fru%2Fwp-admin%2Fedit.php%3Fpost_type%3Dshop_order&action=unifaun_pdfs&m=0&_customer_user=&paged=1&post%5B%5D=10839&action2=-1
				// merge_tab.attr('href','');
				// tabs.append();



				parent.find('p').prepend(tabs);
			}
		}


        /**
         * Change form fields for account type
         */
        if( 'po_SE' == pacsoft.pacsoft_account_type  || 'ufo_SE' == pacsoft.pacsoft_account_type ){
            $( "input[name='pacsoft_api_secret_id']" ).parent().parent().parent().hide();
            $( "h2:contains('Credentials APIConnect')" ).hide();
            $( "p:contains('Track and trace and printing from WooCommerce')" ).hide();
		}

        $( "select[name='pacsoft_account_type']" ).on( 'change', function( event ) {
            if( $( "select[name='pacsoft_account_type']" ).val() == 'po_SE' || $( "select[name='pacsoft_account_type']" ).val() == 'ufo_SE'){
                $( "input[name='pacsoft_api_secret_id']" ).parent().parent().parent().hide();
                $( "h2:contains('Credentials APIConnect')" ).hide();
                $( "h2:contains('Credentials Unifaun/Pacsoft')" ).html('Credentials OnlineConnect');
                $( "p:contains('Track and trace and printing from WooCommerce')" ).hide();
			}
			else if( $( "select[name='pacsoft_account_type']" ).val() == 'unifaun_rest' || $( "select[name='pacsoft_account_type']" ).val() == 'pacsoft_rest' ){
                $( "input[name='pacsoft_api_secret_id']" ).parent().parent().parent().show();
                $( "h2:contains('Credentials APIConnect')" ).show();
                $( "h2:contains('Credentials OnlineConnect')" ).html('Credentials Unifaun/Pacsoft');
                $( "p:contains('Track and trace and printing from WooCommerce')" ).show();
			}
        } );

        /**
         * Change form fields for customs
         */

        if( ! $( "input[name='pacsoft_send_customs_declaration']" ).is(':checked')){
            $( 'form table:nth-child(15) tr:nth-child(2)' ).hide();
            $( 'form table:nth-child(15) tr:nth-child(3)' ).hide();
            $( 'form table:nth-child(15) tr:nth-child(4)' ).hide();
        }

        $( "input[name='pacsoft_send_customs_declaration']" ).on( 'change', function( event ) {
        	console.log($( "input[name='pacsoft_send_customs_declaration']" ).is(':checked') );
            if( $( "input[name='pacsoft_send_customs_declaration']" ).is(':checked')){
                $( 'form table:nth-child(15) tr:nth-child(2)' ).show();
                $( 'form table:nth-child(15) tr:nth-child(3)' ).show();
                $( 'form table:nth-child(15) tr:nth-child(4)' ).show();

            }
            else{
                $( 'form table:nth-child(15) tr:nth-child(2)' ).hide();
                $( 'form table:nth-child(15) tr:nth-child(3)' ).hide();
                $( 'form table:nth-child(15) tr:nth-child(4)' ).hide();
            }
        } );


		/**
		 * Add services row
		 */
		$( '.addPacsoftServiceRow' ).on( 'click', function( event ) {
			event.preventDefault();
			
			$( '#pacsoft-services' ).append( Mustache.render( pacsoft.row, { x: $( '#pacsoft-services tr' ).length } ) );
		} );

        /**
		 * Show/hide buy button
         */
        $( 'input[name=pacsoft_license_key]' ).on( 'keyup change', function(){
            var toggle = ( $(this).val().length > 8 ? 'hide' : 'show' );
            $( '.button.pacsoft-buy-license' )[ toggle ]();
        } ).trigger('change');
		
		/**
		 * Remove services row
		 */
		$( 'body' ).delegate( '#pacsoft-services .removeRow', 'click', function( event ) {
			event.preventDefault();
			
			$( this ).closest( 'tr' ).remove();
		} );
		
		/**
		 * Remove message
		 */
		$( 'body' ).delegate( '#pacsoft-message .notice-dismiss', 'click', function( event ) {
			event.preventDefault();
			
			$( '#pacsoft-message' ).remove();
		} );
		
		/**
		 * Sync order to Pacsoft/Unifaun
		 *
		 * @param orderId
		 * @param serviceId
		 */
		function syncOrder( selector, orderId, serviceId, force )
		{
			var loader = $( selector ).siblings( '.pacsoft-spinner' );
			var status = $( selector ).siblings( '.pacsoft-status' );

			$.ajax( {
				url: window.ajaxurl,
				data: {
			        action: 'pacsoft_sync_order',
			        order_id: orderId,
			        service_id: serviceId,
			        force: force
				},
				type: "post",
				success: function( response ) {
					loader.css( { visibility: "hidden" } );
					
					if( ! response.error ) {
						status.removeClass( 'pacsoft-icon-cross' ).addClass( 'pacsoft-icon-tick' );
						
						$( '#wpbody .wrap h1' ).after( Mustache.render( pacsoft.notice, {
							type: "success",
							message: response.message
						} ) );
					}
					else {
						$( '#wpbody .wrap h1' ).after( Mustache.render( pacsoft.notice, {
							type: "error",
							message: response.message
						} ) );
						var pacsoftMessageElement = $('#pacsoft-message');
						pacsoftMessageElement.show();
						$('html, body').animate({scrollTop: pacsoftMessageElement.offset().top - 100});
					}
					status.show();
				},
				dataType: "json"
			} );
		}

		/**
		 * Enables chosen for selectors
		 */
		function chosify(){
			let selector1=$('td.column-id > select:not(.chosen)');
			let selector2=$('td.column-service > select:not(.chosen)');
			let selector3=$('td.column-shipping_method_id > select:not(.chosen)');
			selector1.chosen();
			selector2.chosen();
			selector3.chosen();
			selector1.addClass('chosen');
			selector2.addClass('chosen');
			selector3.addClass('chosen');
		}

		/**
		 * Sync order to Pacsoft/Unifaun
		 */
		$( '.syncOrderToPacsoft' ).on( 'click', function( event ) {
			event.preventDefault();

			var selector = this;
			var orderId = $( this ).data( 'order' );
			var serviceId = $( this ).data( 'service' );
			var loader = $( this ).siblings( '.pacsoft-spinner' );
			var status = $( this ).siblings( '.pacsoft-status' );
			var shiftHeld = false;

			$( '#pacsoft-message' ).remove();
			
			loader.css( { visibility: "visible" } );
			status.hide();

			// Determine if shift is being held down and force sync
			$(document).click(function(e) {
			    if (e.shiftKey) {
			        shiftHeld = true;
			    } 
			});

			if ('1' == $(this).data('is-kss')) {
				loader.css({visibility: "visible"});

				syncOrder(selector, orderId, $('.pacsoft-services').val(), shiftHeld);
				return;
			}

			if( ! $( this ).data( 'service' ) ) {
				$( '#pacsoft-sync-options-dialog' ).remove();
				$( 'body' ).append( window.pacsoftSyncOptionsDialog );
				
				var width = $( window ).width() * 0.7;
				var height = $( window ).height() * 0.7;
				
				tb_show( pacsoftI18n[ 'Sync order %d to Pacsoft/Unifaun' ].replace( '%d', orderId ), '#TB_inline?width=' + width + '&height=' + height + '&inlineId=pacsoft-sync-options-dialog' );
				
				$( '.syncPacsoftOrderWithOptions' ).on( 'click', function( event ) {
					event.preventDefault();

					tb_remove();
					loader.css( { visibility: "visible" } );

					syncOrder( selector, orderId, $( '.pacsoft-services' ).val(), shiftHeld );
					$( '#pacsoft-sync-options-dialog' ).remove();
				} );
				
				// When dialog is closed, remove it and stop the loader / spinner
				$( '#TB_closeWindowButton, #TB_overlay' ).on( 'click', function(e) {
					e.preventDefault();
					close_dialog();
				} );
				
				function close_dialog() {
					tb_remove();
					loader.css( { visibility: "hidden" } );
					status.show();
					$("#pacsoft-sync-options-dialog").hide();
				}

				$(document).keyup( function(e) {
					if ( 27 == e.keyCode ) {
						close_dialog();
					}
				});

				$("#TB_ajaxContent input.filter").on("change paste keyup", function() {
					var str = $(this).val();
					if ( ! str.match(/[A-Za-z]/) ) {
						$( "#TB_ajaxContent .services-to-filter > li" ).show();
					} else {
						remove( " " );
						remove( "\t" );
						function remove( subStr ) {
							var array = str.split( subStr );
							var newStr = "";
							array.forEach( function( str ) {
								if ( "" != str ) {
									newStr += " " + str;
								}
							} );
							str = newStr.trim();
						}
						$( "#TB_ajaxContent .services-to-filter > li" ).hide();
						var pattern = new RegExp(str, "i");
						$( "#TB_ajaxContent .services-to-filter > li" ).each( function( index ) {
							if ( $( this ).html().match( pattern ) ) {
								$( this ).show();
							}
						});
					}
				});
				$( ".services-to-filter > li").click(function() {
					if ( $( this ).hasClass( "selected" ) ) {
						$( this ).removeClass( "selected" );
						$(".selected-service-indicator .selected-service").html( pacsoftI18n["No service selected, please select one in the list below."]);
						$(".form-table .pacsoft-services").val( 0 );
					} else {
						var val = $( this ).get( -1 ).getAttribute( "value" );
						$(".form-table .pacsoft-services").val( val );
						$( ".services-to-filter > li.selected" ).removeClass( "selected" );
						$( this ).addClass( "selected" );
						$(".selected-service-indicator .selected-service").html( $( this ).html() );
					}
				});
				
                $('li[data-woocommerce-pacsoft-service-base-country="'+pacsoft.choosen_base_country+'"]').removeAttr("hidden");

				$("#pacsoft-sync-options-dialog").show();
			}
			else {
				syncOrder( selector, orderId, serviceId, shiftHeld );
			}
		} );
		
		/**
		 * Print Pacsoft/Unifaun order
		 */
		$( '.printPacsoftOrder' ).on( 'click', function( event ) {
			event.preventDefault();
			
			var orderId = $( this ).data( 'order-id' );
			var loader = $( this ).siblings( '.pacsoft-spinner' );
			var status = $( this ).siblings( '.pacsoft-status' );
			
			loader.css( { visibility: "visible" } );
			status.hide();
			
			$.ajax( {
				url: window.ajaxurl,
				data: {
					action: 'pacsoft_print_order',
					order_id: orderId
				},
				type: "post",
				success: function( response ) {
					loader.css( { visibility: "hidden" } );
					status.show();
					
					if( response.error ) {
						$( '#wpbody .wrap h1' ).after( Mustache.render( pacsoft.notice, {
							type: "error",
							message: response.message
						} ) );
						$( 'html, body' ).animate( { scrollTop: $( '#pacsoft-message' ).offset().top - 100 } );
					}
					else if( response ) {

						if ( response.hasOwnProperty( 'url' ) ){
							var width = $( window ).width() * 0.8;
							var height = $( window ).height() * 0.8;

							tb_show( pacsoftI18n[ 'Print Pacsoft/Unifaun order' ], response.url + '&TB_iframe=1&width=' + width + '&height=' + height );

						}
						else{
							var width = $( window ).width() * 0.8;
							var height = $( window ).height() * 0.8;

							let popup = $('<div></div>');
							popup.css('display', 'none');
							$('#pacsoft-order-pdf-thickbox').remove();
							popup.attr('id', 'pacsoft-order-pdf-thickbox');
							let contents = popup.find('p');
							generate_order_PDFs_preview(response, popup);
							//popup.html();
							$('body').prepend(popup);
							//tb_show( pacsoftI18n[ 'Print Pacsoft/Unifaun order' ], response.url + '&TB_iframe=1&width=' + width + '&height=' + height );
							tb_show(pacsoftI18n[ 'Print Pacsoft/Unifaun order' ], '/?TB_inlinewidth=' + width + '&height=' + height + '&inlineId=pacsoft-order-pdf-thickbox');
							setTimeout(()=>{
							},1000);
						}
						
					} else {
						// Nothing to render - empty response
						$( '#wpbody .wrap h1' ).after( Mustache.render( pacsoft.notice, {
							type: "warning",
							message: 'No printable media for order ' + orderId + '...'
						} ) );
						$( 'html, body' ).animate( { scrollTop: $( '#pacsoft-message' ).offset().top - 100 } );
					}
				},
				dataType: "json"
			} );
		} );

		$('.form-table select[name="pacsoft_base_country"]').change( function() {
            var base_country = $('.form-table select[name="pacsoft_base_country"]').val();
            $('#pacsoft-services option[data-woocommerce-pacsoft-service-base-country]').attr("hidden", true);
            $('#pacsoft-services option[data-woocommerce-pacsoft-service-base-country="'+base_country+'"]').removeAttr("hidden");
		});

		chosify();
		$('a.addPacsoftServiceRow').on('click', ()=>{setTimeout(chosify,50);})
	} );
} )( jQuery );
