<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Campaign Calendar View', 'hh-campaign-cms' ); ?></h1>
    <hr class="wp-header-end">

    <div id="fullcalendar-container" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 1px 1px rgba(0,0,0,.04);max-width:1000px;">
        <div id="calendar"></div>
    </div>
</div>

<style>
    .fc-event.calendar-event-live {
        background-color: #46b450;
        border-color: #46b450;
    }
    .fc-event.calendar-event-upcoming {
        background-color: #0073aa;
        border-color: #0073aa;
    }
    .fc-event.calendar-event-paused {
        background-color: #ffb900;
        border-color: #ffb900;
    }
    .fc-event.calendar-event-expired {
        background-color: #a00;
        border-color: #a00;
    }
    .fc-event.calendar-event-default {
        background-color: #72777c;
        border-color: #72777c;
    }
</style>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            navLinks: true, 
            editable: false,
            eventLimit: true,
            events: function(fetchInfo, successCallback, failureCallback) {
                jQuery.ajax({
                    url: hhCampaignCalendar.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'hh_campaign_cms_get_calendar_events',
                        nonce: hhCampaignCalendar.nonce
                    },
                    success: function(response) {
                        if (response) {
                            successCallback(response); 
                        } else {
                            failureCallback();
                            console.error('HH_Campaign_CMS: Failed to fetch calendar events.');
                        }
                    },
                    error: function(xhr, status, error) {
                        failureCallback();
                        console.error('HH_Campaign_CMS: AJAX Error fetching calendar events:', status, error);
                    }
                });
            },
            eventDidMount: function(info) {
                if (typeof jQuery.fn.tooltip !== 'undefined' && info.event.extendedProps) {
                    var tooltip_content = '<strong>' + info.event.title + '</strong><br>';
                    if (info.event.extendedProps.internal_reference) {
                        tooltip_content += 'Ref: ' + info.event.extendedProps.internal_reference + '<br>';
                    }
                    if (info.event.extendedProps.status) {
                        tooltip_content += 'Status: ' + info.event.extendedProps.status + '<br>';
                    }
                    if (info.event.extendedProps.notes) {
                        tooltip_content += 'Notes: ' + info.event.extendedProps.notes;
                    }

                    jQuery(info.el).attr('title', tooltip_content).tooltip({
                        content: function() { return jQuery(this).attr('title'); },
                        track: true,
                        position: { my: "center bottom-10", at: "center top", collision: "flipfit" }
                    });
                }
            }
        });
        calendar.render();
    });
</script>
