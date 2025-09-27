(function( $ ) {
        'use strict';

        var dayOrder = [ 1, 2, 3, 4, 5, 6, 0 ];

        var defaultStrings = {
                days: {
                        1: 'Mondays',
                        2: 'Tuesdays',
                        3: 'Wednesdays',
                        4: 'Thursdays',
                        5: 'Fridays',
                        6: 'Saturdays',
                        0: 'Sundays'
                },
                every_day: 'Every day',
                list: {
                        two: '%1$s and %2$s',
                        serial: '%1$s, and %2$s',
                        join: ', '
                },
                time: {
                        all_day: 'All Day',
                        from_to: 'from %1$s to %2$s',
                        from: 'from %s onward',
                        until: 'until %s'
                },
                dates: {
                        between: 'between %1$s and %2$s',
                        starting: 'starting %s',
                        until: 'until %s'
                }
        };

        var summaryStrings = $.extend( true, {}, defaultStrings, window.vuwu_channel_schedule_summary || {} );

        function formatString( template ) {
                var args = Array.prototype.slice.call( arguments, 1 );

                if ( ! template ) {
                        return '';
                }

                var formatted = template.replace( /%([0-9]+)\$s/g, function( match, position ) {
                        var index = parseInt( position, 10 ) - 1;
                        return typeof args[ index ] !== 'undefined' ? args[ index ] : '';
                } );

                return formatted.replace( /%s/g, function() {
                        return args.length ? args.shift() : '';
                } );
        }

        function capitalize( text ) {
                if ( ! text ) {
                        return text;
                }

                return text.charAt( 0 ).toUpperCase() + text.slice( 1 );
        }

        function getOrdinalSuffix( day ) {
                var remainderHundred = day % 100;

                if ( remainderHundred >= 11 && remainderHundred <= 13 ) {
                        return 'th';
                }

                switch ( day % 10 ) {
                        case 1:
                                return 'st';
                        case 2:
                                return 'nd';
                        case 3:
                                return 'rd';
                        default:
                                return 'th';
                }
        }

        function formatTimeValue( value ) {
                if ( ! value ) {
                        return '';
                }

                var parts = value.split( ':' );

                if ( parts.length < 2 ) {
                        return '';
                }

                var hour = parseInt( parts[0], 10 );
                var minute = parseInt( parts[1], 10 );

                if ( isNaN( hour ) || isNaN( minute ) ) {
                        return '';
                }

                var time = new Date( Date.UTC( 1970, 0, 1, hour, minute ) );

                return time.toLocaleTimeString( undefined, { hour: 'numeric', minute: '2-digit' } );
        }

        function formatDateValue( value, comparison ) {
                if ( ! value ) {
                        return '';
                }

                var date = new Date( value + 'T12:00:00' );

                if ( isNaN( date.getTime() ) ) {
                        return '';
                }

                var day = date.getDate();
                var ordinal = day + getOrdinalSuffix( day );
                var month = date.toLocaleString( undefined, { month: 'long' } );
                var result = ordinal + ' ' + month;
                var currentYear = ( new Date() ).getFullYear();
                var year = date.getFullYear();
                var comparisonYear = comparison ? ( new Date( comparison + 'T12:00:00' ) ).getFullYear() : year;

                if ( year !== currentYear || ( comparison && year !== comparisonYear ) ) {
                        result += ' ' + year;
                }

                return result;
        }

        function humanizeList( items ) {
                items = items.filter( function( item ) {
                        return item;
                } );

                var count = items.length;

                if ( count === 0 ) {
                        return '';
                }

                if ( count === 1 ) {
                        return items[0];
                }

                if ( count === 2 ) {
                        return formatString( summaryStrings.list.two, items[0], items[1] );
                }

                var last = items[ count - 1 ];
                var initial = items.slice( 0, -1 ).join( summaryStrings.list.join );

                return formatString( summaryStrings.list.serial, initial, last );
        }

        function formatScheduleDays( days ) {
                var seen = {};
                var normalized = [];

                $.each( days, function( index, value ) {
                        var day = parseInt( value, 10 );

                        if ( isNaN( day ) || seen[ day ] ) {
                                return;
                        }

                        seen[ day ] = true;
                        normalized.push( day );
                } );

                if ( normalized.length === 0 ) {
                        return summaryStrings.every_day;
                }

                normalized.sort( function( a, b ) {
                        return dayOrder.indexOf( a ) - dayOrder.indexOf( b );
                } );

                if ( normalized.length === dayOrder.length ) {
                        return summaryStrings.every_day;
                }

                var labels = [];

                normalized.forEach( function( day ) {
                        if ( summaryStrings.days.hasOwnProperty( day ) ) {
                                labels.push( summaryStrings.days[ day ] );
                        }
                } );

                return labels.length ? humanizeList( labels ) : summaryStrings.every_day;
        }

        function formatScheduleTime( start, end ) {
                var startText = formatTimeValue( start );
                var endText = formatTimeValue( end );

                if ( startText && endText ) {
                        return capitalize( formatString( summaryStrings.time.from_to, startText, endText ) );
                }

                if ( startText ) {
                        return capitalize( formatString( summaryStrings.time.from, startText ) );
                }

                if ( endText ) {
                        return capitalize( formatString( summaryStrings.time.until, endText ) );
                }

                return summaryStrings.time.all_day;
        }

        function formatScheduleDates( start, end ) {
                var startText = formatDateValue( start, end );
                var endText = formatDateValue( end, start );

                if ( startText && endText ) {
                        return formatString( summaryStrings.dates.between, startText, endText );
                }

                if ( startText ) {
                        return capitalize( formatString( summaryStrings.dates.starting, startText ) );
                }

                if ( endText ) {
                        return capitalize( formatString( summaryStrings.dates.until, endText ) );
                }

                return '';
        }

        function updateScheduleSummary( $row ) {
                if ( ! $row || ! $row.length ) {
                        return;
                }

                var index = $row.attr( 'data-index' );
                var data = {
                        date_start: $row.find( 'input[name$="[date_start]"]' ).val(),
                        date_end: $row.find( 'input[name$="[date_end]"]' ).val(),
                        time_start: $row.find( 'input[name$="[time_start]"]' ).val(),
                        time_end: $row.find( 'input[name$="[time_end]"]' ).val(),
                        days: []
                };

                $row.find( 'input[name$="[days][]"]:checked' ).each( function() {
                        data.days.push( $( this ).val() );
                } );

                var parts = [];
                var dayPart = formatScheduleDays( data.days );
                var timePart = formatScheduleTime( data.time_start, data.time_end );
                var datePart = formatScheduleDates( data.date_start, data.date_end );

                if ( dayPart ) {
                        parts.push( dayPart );
                }

                if ( timePart ) {
                        parts.push( timePart );
                }

                if ( datePart ) {
                        parts.push( datePart );
                }

                var summary = $.trim( parts.join( ' ' ) ).replace( /\s+/g, ' ' );

                if ( summary && summary.slice( -1 ) !== '.' ) {
                        summary += '.';
                }

                if ( ! summary ) {
                        summary = summaryStrings.every_day + ' ' + summaryStrings.time.all_day + '.';
                }

                var $summaryRow = $row.next( '.vuwu-channel-schedule-row-summary[data-index="' + index + '"]' );

                if ( $summaryRow.length ) {
                        $summaryRow.find( '.vuwu-channel-schedule-summary' ).text( summary );
                }
        }

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

                var $row = $container.children( '.vuwu-channel-schedule-row' ).last();
                updateScheduleSummary( $row );
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

                var $row = $container.children( '.vuwu-channel-schedule-row' ).last();
                updateScheduleSummary( $row );
        }

        function removeScheduleRow( event ) {
                event.preventDefault();

                var $row = $( event.currentTarget ).closest( '.vuwu-channel-schedule-row' );
                var index = $row.attr( 'data-index' );
                var $container = $row.closest( '.vuwu-channel-schedule-rows' );
                var $summaryRow = $row.next( '.vuwu-channel-schedule-row-summary[data-index="' + index + '"]' );

                if ( $summaryRow.length ) {
                        $summaryRow.remove();
                }

                $row.remove();

                ensurePlaceholderRow( $container );
        }

        $( document ).on( 'click', '.vuwu-add-schedule-row', addScheduleRow );
        $( document ).on( 'click', '.vuwu-remove-schedule-row', removeScheduleRow );

        $( document ).on( 'input change', '.vuwu-channel-schedule-row input, .vuwu-channel-schedule-row select', function() {
                updateScheduleSummary( $( this ).closest( '.vuwu-channel-schedule-row' ) );
        } );

        $( document ).ready( function() {
                $( '.vuwu-channel-schedule-rows' ).each( function() {
                        var $container = $( this );

                        ensurePlaceholderRow( $container );

                        $container.children( '.vuwu-channel-schedule-row' ).each( function() {
                                updateScheduleSummary( $( this ) );
                        } );
                } );
        } );
})( jQuery );
