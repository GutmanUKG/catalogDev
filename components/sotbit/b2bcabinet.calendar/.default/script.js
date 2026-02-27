if (typeof FullCalendarStyling === 'undefined') {
    let FullCalendarStyling = function () {

        const _componentFullCalendarStyling = function (events) {
            if (typeof FullCalendar == 'undefined') {
                console.warn('Warning - Fullcalendar files are not loaded.');
                return;
            }

            FullCalendar.globalLocales.push(function () {
                'use strict';

                var ru = {
                    code: 'ru',
                    week: {
                        dow: 1, // Monday is the first day of the week.
                        doy: 4, // The week that contains Jan 4th is the first week of the year.
                    },
                    buttonText: {
                        prev: BX.message('BTN_PREV'),
                        next: BX.message('BTN_NEXT'),
                        today: BX.message('BTN_TODAY'),
                        month: BX.message('BTN_MONTH'),
                        week: BX.message('BTN_WEEK'),
                        day: BX.message('BTN_DAY'),
                        list: BX.message('BTN_LIST'),
                    },
                    weekText: BX.message('BTN_WEEK_SHORT'),
                    allDayText: BX.message('BTN_ALL_DAY'),
                    moreLinkText: function (n) {
                        return BX.message('BTN_MORE_LINK') + n
                    },
                    noEventsText: BX.message('BTN_NO_EVENTS'),
                };

                return ru;

            }());

            const calendarEventColorsElement = document.querySelector('.b2bcabinet-mainpage__calendar');

            if (calendarEventColorsElement) {
                window.calendarB2BcabinetMainpage = new FullCalendar.Calendar(calendarEventColorsElement, {
                    locale: 'ru',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek,dayGridDay'
                    },
                // initialView: 'listWeek',
                    navLinks: true,
                    businessHours: true,
                    editable: false,
                    selectable: true,
                    direction: document.dir == 'rtl' ? 'rtl' : 'ltr',
                    events: events,
                    eventDidMount: function(info) {
                        info.el.style.backgroundColor = info.event.extendedProps.color;
                        info.el.style.borderColor = info.event.extendedProps.color;
                        var dotEl = info.el.getElementsByClassName('fc-daygrid-event-dot')[0];
                        if (dotEl) {
                            dotEl.style.borderColor = info.event.extendedProps.color;
                        }

                    }
                });

                window.calendarB2BcabinetMainpage.render();

                $('.sidebar-control').on('click', function () {
                    window.calendarB2BcabinetMainpage.updateSize();
                });
            }

            window.calendarB2BcabinetMainpage.render();

            $('.sidebar-control').on('click', function () {
                window.calendarB2BcabinetMainpage.updateSize();
            });

        };

        return {
            init: function (events) {
                _componentFullCalendarStyling(events);
            }
        }
    }();

    window.addEventListener('load', function () {
        const waitCalendar = document.querySelector(".fullcalendar__spinner");

        var request = BX.ajax.runComponentAction('sotbit:b2bcabinet.calendar', 'getEvents', {
            mode: 'class',
        });

        request.then(function (response) {
            var events = JSON.parse(response.data);
            waitCalendar.remove();
            FullCalendarStyling.init(events);
        });
    });
}