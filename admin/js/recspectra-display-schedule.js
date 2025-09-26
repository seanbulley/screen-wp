(function( $ ) {
        'use strict';

        function getNextScheduleIndex( $container ) {
                var maxIndex = -1;

                $container.find( '.recspectra-channel-schedule-row' ).each( function() {
                        var index = parseInt( $( this ).attr( 'data-index' ), 10 );
                        if ( ! isNaN( index ) && index > maxIndex ) {
                                maxIndex = index;
                        }
                } );

                return maxIndex + 1;
        }

        function ensurePlaceholderRow( $container ) {
                if ( $container.children().length ) {
                        return;
                }

                var template = $( '#recspectra-channel-schedule-row-template' ).html();

                if ( ! template ) {
                        return;
                }

                template = template.replace( /__INDEX__/g, 0 );
                $container.append( template );
        }

        function addScheduleRow( event ) {
                event.preventDefault();

                var $button = $( event.currentTarget );
                var $container = $button.closest( '.inside' ).find( '.recspectra-channel-schedule-rows' );
                var template = $( '#recspectra-channel-schedule-row-template' ).html();

                if ( ! template || ! $container.length ) {
                        return;
                }

                var nextIndex = getNextScheduleIndex( $container );
                var rowHtml = template.replace( /__INDEX__/g, nextIndex );

                $container.append( rowHtml );
        }

        function removeScheduleRow( event ) {
                event.preventDefault();

                var $row = $( event.currentTarget ).closest( '.recspectra-channel-schedule-row' );
                var $container = $row.closest( '.recspectra-channel-schedule-rows' );

                $row.remove();
                ensurePlaceholderRow( $container );
        }

        $( document ).on( 'click', '.recspectra-add-schedule-row', addScheduleRow );
        $( document ).on( 'click', '.recspectra-remove-schedule-row', removeScheduleRow );

        $( document ).ready( function() {
                $( '.recspectra-channel-schedule-rows' ).each( function() {
                        ensurePlaceholderRow( $( this ) );
                } );
        } );
})( jQuery );
