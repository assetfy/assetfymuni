import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';

function initializeCalendar() {
    var calendarEl = document.getElementById('calendar')
    if (!calendarEl) return; // Asegúrate de que el elemento exista

    var events = [];

    try {
        events = calendarEl.dataset.events ? JSON.parse(calendarEl.dataset.events) : [];
    } catch (e) {
        console.error('Error parsing JSON:', e);
        console.error('Invalid JSON:', calendarEl.dataset.events);
    }

    var calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, bootstrap5Plugin],
        initialView: 'dayGridMonth',
        events: events,
        height: 'auto',
        locale: esLocale,
        themeSystem: 'bootstrap5',
        headerToolbar: {
            left: 'prev,dayGridMonth',
            center: 'title',
            right: 'today,next',
        },
        titleFormat: {
            month: 'short',
            year: 'numeric',
        },
        fixedWeekCount: false,
        eventContent: function(info) {
            if (info.view.type === 'dayGridMonth') {
                return { html: '' };
            } else {
                const title = info.event.title;
                const start = info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const end = info.event.end ? info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'No end time';
                return { html: `<div>${title}<br>${start} - ${end}</div>` };
            }
        },
        eventDidMount: function(info) {
            if (info.view.type === 'dayGridMonth') {
                info.el.style.backgroundColor = 'red';
                info.el.style.borderColor = 'red';
                info.el.style.color = '#2479c7';
                info.el.style.height = '100%';
                info.el.style.width = '100%';
                info.el.style.position = 'absolute';
                info.el.style.top = '0';
                info.el.style.left = '0.5em';
                info.el.style.right = '0';
                info.el.style.bottom = '0.5em';
                tippy(info.el, {
                    content: `${info.event.title} - ${info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`,
                });
            }
        }
    });
    calendar.render();
}

// Asigna la función al objeto window para hacerla global
window.initializeCalendar = initializeCalendar;

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('calendar')) {
        window.initializeCalendar();
    }

    document.addEventListener('livewire:load', () => {
        if (document.getElementById('calendar')) {
            window.initializeCalendar();
        }

        Livewire.hook('message.processed', (message, component) => {
            if (document.getElementById('calendar')) {
                window.initializeCalendar();
            }
        });
    });
});
