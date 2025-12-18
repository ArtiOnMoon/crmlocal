/**
 * Глобальный инстанс таймлайна Vis.js. Инициализируется/пересоздаётся при каждом вызове loadTimeline().
 * @type {vis.Timeline | null}
 */
let timelineInstance = null;

/**
 * Переключает видимость таймлайна и при раскрытии — асинхронно загружает данные.
 * @public
 * @async
 */
window.toggleTimeline = async function () {
    const wrapper = document.getElementById('timeline-wrapper');
    if (!wrapper) return;

    wrapper.classList.toggle('timeline-collapsed');

    // При раскрытии — загружаем данные
    if (!wrapper.classList.contains('timeline-collapsed')) {
        await loadTimeline();
    }
};

/**
 * Извлекает текущие значения фильтров из DOM.
 * @returns {{ status: number[], users: number[], period: string, periodStart?: string, periodEnd?: string }}
 */

function getSelectedFilters() {
    // 🔹 Статусы
    const status = Array.from(document.querySelectorAll('.service_filter'))
        .filter(el => el.checked)
        .map(el => {
            const match = el.id.match(/status_(\d+)/);
            return match ? parseInt(match[1], 10) : null;
        })
        .filter(id => id !== null);

    // 🔹 Пользователи
    const users = Array.from(document.querySelectorAll('.user_multiselect'))
        .filter(el => el.checked)
        .map(el => {
            const val = parseInt(el.value, 10);
            return isNaN(val) || val <= 0 ? null : val;
        })
        .filter(id => id !== null);

    // 🔹 КОМПАНИИ - получаем из существующего селектора, но учитываем галочку
    const ignoreCompany = document.getElementById('timeline-ignore-company')?.checked;
    let companies = [];
    
    if (!ignoreCompany) {
        const companySelect = document.getElementById('service_our_company');
        companies = companySelect && companySelect.value ? [parseInt(companySelect.value, 10)] : [];
    }

    // 🔹 Период
    const periodSelect = document.getElementById('timeline-period');
    const period = periodSelect?.value || '1m';

    let periodStart = null, periodEnd = null;
    if (period === 'custom') {
        const startEl = document.getElementById('timeline-start-date');
        const endEl = document.getElementById('timeline-end-date');
        periodStart = startEl?.value || null;
        periodEnd = endEl?.value || null;
        if (periodStart && periodEnd && periodStart > periodEnd) {
            [periodStart, periodEnd] = [periodEnd, periodStart];
        }
    }

    return { 
        status, 
        users, 
        companies, 
        period, 
        periodStart, 
        periodEnd,
        ignoreCompany // ← передаем флаг игнорирования компаний
    };
}

/**
 * Форматирует номер заявки в единый вид SR-COMP-00000.
 * Используется как в groupTemplate, так и при клике на элементы.
 *
 * @param {Object} group - Данные группы (от сервера или DataSet)
 * @param {string} [group.serviceOurComp] - Компания (по умолчанию 'MSS')
 * @param {number|string} [group.serviceNo] - Номер заявки (может быть '0' или пусто)
 * @param {number} [group.serviceId] - ID заявки как fallback
 * @returns {string} Например: "SR-MSS-00123"
 */
function formatServiceRef(group) {
    const comp = (String(group.serviceOurComp ?? '').trim() || 'MSS').toUpperCase();

    let noStr = String(group.serviceNo ?? '').trim();
    // Если serviceNo отсутствует или "0" — fallback на serviceId
    if (!noStr || noStr === '0') {
        noStr = String(group.serviceId ?? 0);
    }

    // Оставляем только цифры, приводим к 5-значному представлению
    const digitsOnly = noStr.replace(/\D/g, '');
    const paddedNo = digitsOnly
        ? (digitsOnly.length <= 5 ? digitsOnly.padStart(5, '0') : digitsOnly.slice(-5))
        : '00000';

    return `SR-${comp}-${paddedNo}`;
}

/**
 * Асинхронно загружает и отображает таймлайн.
 * Обрабатывает фильтры, делает POST-запрос, конструирует Vis.js Timeline.
 *
 * @async
 */
/**
 * Асинхронно загружает и отображает таймлайн.
 * Показывает анимированные "..." во время загрузки.
 *
 * @async
 */
async function loadTimeline() {
    const container = document.getElementById('timeline-container');
    if (!container) {
        console.error('❌ #timeline-container не найден');
        return;
    }

    container.innerHTML = `
        <div class="timeline-loading">
            Загрузка данных
            <span class="timeline-loading-dots"></span>
        </div>
    `;

    const filters = getSelectedFilters();

    try {
        const response = await fetch('ajax/service_timeline.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
        status: JSON.stringify(filters.status),
        users: JSON.stringify(filters.users),
        companies: JSON.stringify(filters.companies || []),
        ignore_company: filters.ignoreCompany ? '1' : '0', // ← передаем флаг
        period: filters.period,
        period_start: filters.periodStart || '',
        period_end: filters.periodEnd || ''
    })
});

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        const itemsData = data.items || [];
        const groupsData = data.groups || [];

        if (!itemsData.length) {
            container.innerHTML = "<div style='padding:20px; text-align:center;'>Заявок не найдено</div>";
            return;
        }

        const itemTemplate = function (item) {
            return `<div class="timeline-item-content">${item.content}</div>`;
        };

        const itemsDS = new vis.DataSet(
            itemsData.map(item => ({
                ...item,
                start: item.start ? new Date(item.start) : new Date(),
                end: item.end ? new Date(item.end) : null,
            }))
        );

        const groupsDS = new vis.DataSet(groupsData);

        if (timelineInstance) {
            try {
                timelineInstance.destroy();
            } catch (e) {
                console.warn('⚠️ Ошибка при уничтожении старого timeline:', e);
            }
            timelineInstance = null;
        }

        container.innerHTML = '';

        timelineInstance = new vis.Timeline(container, itemsDS, groupsDS, {
            start: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
            end: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0),
            orientation: 'top',
            margin: { item: { horizontal: 0, vertical: 5 } },
            stack: false,
            verticalScroll: true,
            horizontalScroll: true,
            zoomKey: 'ctrlKey',
            template: itemTemplate, // ← подключаем кастомный шаблон
            groupTemplate: function (group) {
                const wrapper = document.createElement('div');
                wrapper.className = 'timeline-group-header';

                const statusDot = document.createElement('span');
                statusDot.className = 'group-status-dot';
                const statusClass = (() => {
                    switch (group.status) {
                        case 1: return 'status_request';
                        case 2: return 'status_quotation';
                        case 3: return 'status_confirmed';
                        case 5: return 'status_canceled';
                        case 6: return 'status_complited';
                        case 7: return 'status_follow-up';
                        case 8: return 'status_expired';
                        case 9: return 'status_post-processing';
                        default: return 'status_unknown';
                    }
                })();
                statusDot.classList.add(statusClass);
                statusDot.title = 'Статус заявки';

                const vesselLink = document.createElement('a');
                vesselLink.className = 'group-vessel-link';
                vesselLink.textContent = group.vesselName || '—';
                vesselLink.href = '#';
                vesselLink.dataset.vesselId = group.vesselId || '';
                vesselLink.title = `Судно: ${group.vesselName || 'не указано'}`;
                vesselLink.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const vesselId = parseInt(group.vesselId, 10);
                    if (!isNaN(vesselId) && typeof vessel_view === 'function') {
                        vessel_view(vesselId);
                    }
                };

                const serviceSection = document.createElement('div');
                serviceSection.className = 'group-service-section';

                const separator = document.createElement('span');
                separator.className = 'group-separator';
                separator.textContent = '⟶';

                const serviceRef = formatServiceRef(group);
                const serviceLink = document.createElement('a');
                serviceLink.className = 'group-service-link';
                serviceLink.textContent = serviceRef;
                serviceLink.href = '#';
                serviceLink.dataset.serviceRef = serviceRef;
                serviceLink.title = `Заявка: ${serviceRef}`;
                serviceLink.onclick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof view_link === 'function') {
                        view_link(serviceRef);
                    }
                };

                serviceSection.append(separator, serviceLink);
                wrapper.append(statusDot, vesselLink, serviceSection);
                return wrapper;
            },
            groupOrder: function(a, b) {
                return (a.sortKey || '').localeCompare(b.sortKey || '');
            },
            showMajorLabels: true,
            showCurrentTime: false,
            format: {
                minorLabels: { day: 'D', week: 'D MMM', month: 'MMMM', year: 'YYYY' },
                majorLabels: { day: 'dddd, D MMMM YYYY', week: 'D MMMM YYYY', month: 'MMMM YYYY', year: 'YYYY' }
            },
            zoomMin: 1000 * 60 * 60 * 24,
            zoomMax: 1000 * 60 * 60 * 24 * 31 * 3,
             tooltip: {
                show: true,
                followMouse: true, // Тултип следует за курсором
                overflowMethod: 'flip', // Поведение при достижении границ
                delay: 100, // Задержка перед показом
                template: function (itemData) {
                    // 🔹 Для контекстных элементов - специальное сообщение
                    if (itemData.is_contextual) {
                        return "Вы видите эту заявку, так как есть смежные заявки";
                    }
                    
                    // 🔹 Для обычных элементов - стандартная информация
                    const parts = [];
                    if (itemData.customer) {
                        parts.push(itemData.customer);
                    }
                    if (itemData.engineers) {
                        parts.push(`Исполнители: ${itemData.engineers}`);
                    }
                    
                    return parts.length > 0 ? parts.join('<br>') : 'Заявка';
                }
            }
        });

        timelineInstance.on('click', function (props) {
            if (props.what === 'background') return;
            const groupId = props.group;
            if (!groupId) return;

            const group = groupsDS.get(groupId);
            if (!group) return;

            if (props.what === 'item' && typeof view_link === 'function') {
                view_link(formatServiceRef(group));
            }
        });

    } catch (err) {
        console.error('❌ Ошибка загрузки таймлайна:', err);
        container.innerHTML = `
            <div style='padding:20px; color:red; text-align:center;'>
                Ошибка загрузки: ${err.message || 'неизвестная ошибка'}
            </div>
        `;
    }
}

function initTimelineControls() {
    document.querySelectorAll('.service_filter, .user_multiselect').forEach(el => {
        el.addEventListener('change', loadTimeline);
    });

    // Обработчики period — остаются (уже реализованы)
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
            loadTimeline();
        });
    }

    if (startDateInput && endDateInput) {
        [startDateInput, endDateInput].forEach(el => {
            el.addEventListener('change', () => {
                if (periodSelect.value === 'custom') loadTimeline();
            });
        });
    }

    const wrapper = document.getElementById('timeline-wrapper');
    if (wrapper && !wrapper.classList.contains('timeline-collapsed')) {
        loadTimeline();
    }
}

document.addEventListener('DOMContentLoaded', initTimelineControls);

/**
 * Инициализация DOM-обработчиков при загрузке страницы.
 */
function initTimelineControls() {
    // 🔹 Обновление при изменении фильтров (добавьте companies)
    document.querySelectorAll('.service_filter, .user_multiselect, .company_multiselect').forEach(el => {
        el.addEventListener('change', loadTimeline);
    });

    // 🔹 Чекбокс "загрузить всё"
    const loadAllEl = document.getElementById('timeline-load-all');
    if (loadAllEl) {
        loadAllEl.addEventListener('change', loadTimeline);
    }

    // 🔹 Автозагрузка при открытом таймлайне
    const wrapper = document.getElementById('timeline-wrapper');
    if (wrapper && !wrapper.classList.contains('timeline-collapsed')) {
        loadTimeline();
    }
}

// 🚀 Запуск инициализации после загрузки DOM
document.addEventListener('DOMContentLoaded', initTimelineControls);