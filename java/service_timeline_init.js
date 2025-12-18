/**
 * service_timeline_init.js
 * Инициализация и управление Vis.js Timeline на странице service.php.
 * Подключается после DOMContentLoaded и после загрузки основных скриптов.
 *
 * Зависимости:
 *   - vis-timeline (CDN или локально)
 *   - service_timeline.js (уже содержит toggleTimeline(), loadTimeline())
 *   - jQuery (для совместимости с legacy-кодом)
 *
 * @author YourName
 * @since 2025-11-25
 */

(function () {
    'use strict';

    /**
     * Инициализирует таймлайн: подключает стили, рендерит HTML, навешивает обработчики.
     */
    function initServiceTimeline() {
        // 🔹 Проверка: есть ли место для таймлайна?
        const sideMenu = document.getElementById('side_menu');
        if (!sideMenu) {
            console.warn('⚠️ #side_menu не найден — таймлайн не инициализирован');
            return;
        }

        // 🔹 HTML-строка таймлайна (изолированная, без inline-стилей)
const timelineHTML = `
<!-- 🔹 ТАЙМЛАЙН БЛОК -->
<div id="timeline-wrapper" class="timeline-collapsed">
    <div id="timeline-header" onclick="toggleTimeline()">
        <span>Таймлайн заявок</span>
        <div class="timeline-arrow"></div>
    </div>
    <div id="timeline-body">
        <div class="timeline-periodbox">
            <label>Период:</label>
            <select id="timeline-period">
                <option value="1m">±1 месяц</option>
                <option value="3m">±3 месяца</option>
                <option value="6m">±6 месяцев</option>
                <option value="1y">±1 год</option>
                <option value="custom">Другой период…</option>
            </select>
            <div id="timeline-custom-dates" style="display: none; margin-left: 10px;">
                <input type="date" id="timeline-start-date" style="width: 48%; margin-right: 4%;">
                <input type="date" id="timeline-end-date" style="width: 48%;">
            </div>
        </div>
        <div class="timeline-filterbox">
            <label>
                <input type="checkbox" id="timeline-ignore-company" checked>
                Отключить фильтрацию по компаниям
            </label>
        </div>
        <div id="timeline-container"></div>
    </div>
</div>
<!-- 🔹 /ТАЙМЛАЙН БЛОК -->
`;

        // 🔹 Вставляем перед закрывающим `</div>` #side_menu
        sideMenu.insertAdjacentHTML('beforeend', timelineHTML);

        // 🔹 Подключаем Vis.js (если ещё не загружен)
        if (typeof vis === 'undefined') {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/vis-timeline@7.7.1/styles/vis-timeline-graph2d.min.css';
            document.head.appendChild(link);

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/vis-timeline@7.7.1/standalone/umd/vis-timeline-graph2d.min.js';
            script.onload = () => {
                console.log('✅ Vis.js Timeline загружен');
                // Автозапуск, если таймлайн уже открыт
                const wrapper = document.getElementById('timeline-wrapper');
                if (wrapper && !wrapper.classList.contains('timeline-collapsed')) {
                    typeof loadTimeline === 'function' && loadTimeline();
                }
            };
            document.head.appendChild(script);
        } else {
            // Если Vis.js уже есть — проверяем, открыт ли таймлайн
            const wrapper = document.getElementById('timeline-wrapper');
            if (wrapper && !wrapper.classList.contains('timeline-collapsed')) {
                typeof loadTimeline === 'function' && loadTimeline();
            }
        }

        // 🔹 Обработчик для чекбокса "загрузить всё"
        const loadAllEl = document.getElementById('timeline-load-all');
        if (loadAllEl) {
            loadAllEl.addEventListener('change', () => {
                if (typeof loadTimeline === 'function') {
                    loadTimeline();
                }
            });
        }

        // 🔹 Обработчик для галочки "Отключить фильтрацию по компаниям"
        const ignoreCompanyCheckbox = document.getElementById('timeline-ignore-company');
        if (ignoreCompanyCheckbox) {
            ignoreCompanyCheckbox.addEventListener('change', () => {
                if (typeof loadTimeline === 'function') {
                    loadTimeline();
                }
            });
        }

        // 🔹 Обработчик для селектора компании (только если галочка неактивна)
        const companySelect = document.getElementById('service_our_company');
        if (companySelect) {
            companySelect.addEventListener('change', () => {
                const ignoreCompany = document.getElementById('timeline-ignore-company')?.checked;
                if (!ignoreCompany && typeof loadTimeline === 'function') {
                    loadTimeline();
                }
            });
        }

        // 🔹 Обработчик выбора периода
        const periodSelect = document.getElementById('timeline-period');
        const customDatesDiv = document.getElementById('timeline-custom-dates');
        const startDateInput = document.getElementById('timeline-start-date');
        const endDateInput = document.getElementById('timeline-end-date');

        if (periodSelect) {
            periodSelect.addEventListener('change', () => {
                const val = periodSelect.value;
                if (val === 'custom') {
                    const now = new Date();
                    startDateInput.value = new Date(now.getFullYear(), now.getMonth() - 1, 1).toISOString().split('T')[0];
                    endDateInput.value = new Date(now.getFullYear(), now.getMonth() + 2, 0).toISOString().split('T')[0];
                    customDatesDiv.style.display = 'block';
                } else {
                    customDatesDiv.style.display = 'none';
                }
                if (typeof loadTimeline === 'function') {
                    loadTimeline();
                }
            });
        }

        if (startDateInput && endDateInput) {
            [startDateInput, endDateInput].forEach(el => {
                el.addEventListener('change', () => {
                    if (periodSelect.value === 'custom' && typeof loadTimeline === 'function') {
                        loadTimeline();
                    }
                });
            });
        }
    }

    // 🔹 Запуск после загрузки DOM и всех скриптов
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initServiceTimeline);
    } else {
        // DOM уже загружен — запускаем сразу
        setTimeout(initServiceTimeline, 0);
    }

})();