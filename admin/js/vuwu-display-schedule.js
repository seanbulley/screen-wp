(function( $ ) {
        'use strict';

        function getNextScheduleIndex( $container ) {
                var maxIndex = -1;

                $container.find( '.vuwu-channel-schedule-row' ).each( function() {
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

                var template = $( '#vuwu-channel-schedule-row-template' ).html();

                if ( ! template ) {
                        return;
                }

                template = template.replace( /__INDEX__/g, 0 );
                $container.append( template );
        }

        function addScheduleRow( event ) {
                event.preventDefault();

                var $button = $( event.currentTarget );
                var $container = $button.closest( '.inside' ).find( '.vuwu-channel-schedule-rows' );
                var template = $( '#vuwu-channel-schedule-row-template' ).html();

                if ( ! template || ! $container.length ) {
                        return;
                }

                var nextIndex = getNextScheduleIndex( $container );
                var rowHtml = template.replace( /__INDEX__/g, nextIndex );

                $container.append( rowHtml );
        }

        function removeScheduleRow( event ) {
                event.preventDefault();

                var $row = $( event.currentTarget ).closest( '.vuwu-channel-schedule-row' );
                var $container = $row.closest( '.vuwu-channel-schedule-rows' );

                $row.remove();
                ensurePlaceholderRow( $container );
        }

        $( document ).on( 'click', '.vuwu-add-schedule-row', addScheduleRow );
        $( document ).on( 'click', '.vuwu-remove-schedule-row', removeScheduleRow );

        $( document ).ready( function() {
                $( '.vuwu-channel-schedule-rows' ).each( function() {
                        ensurePlaceholderRow( $( this ) );
                } );
        } );
})( jQuery );
