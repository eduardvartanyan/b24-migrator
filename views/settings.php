<?php
declare(strict_types=1);

use App\Repositories\ClientRepository;

$clientRepository = new ClientRepository();
$domain = htmlspecialchars($_REQUEST['DOMAIN'] ?? '');
$client = $clientRepository->getByDomain($domain);

// TODO: заменишь на репозиторий заданий (JobRepository)
// $jobs = (new JobRepository())->listByDomain($domain);

// Для верстки: можно переключать
$jobs = []; // пусто -> покажет empty-state

// Пример структуры записи задания (когда подключишь данные):
/*
$jobs = [
  [
    'id' => 'job-1',
    'title' => 'CRM Jan 2025',
    'source' => 'portal-a.bitrix24.ru',
    'target' => 'portal-b.bitrix24.ru',
    'entities' => ['Сделки','Контакты','Компании'],
    'status' => 'RUNNING', // DRAFT|READY|RUNNING|PAUSED|DONE|FAILED|STOPPED
    'progress' => 45,
    'updated' => '30.12.2025 10:25',
  ],
];
*/
?>

<style>
    .b24-app-layout {
        display: flex;
        gap: 24px;
        padding: 24px;
        background: #f5f7f8;
        justify-content: flex-start;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    }

    .b24-app-main{
        width: 760px;
        flex-shrink: 0;
    }

    @media (max-width: 1100px) {
        .b24-app-layout {
            flex-direction: column;
        }

        .b24-app-main {
            width: 100%;
        }
    }

    /* --- Common UI blocks (Bitrix24-like) --- */
    .b24-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .b24-page-title {
        font-size: 22px;
        font-weight: 700;
        color: #2f343b;
        margin: 0;
        line-height: 1.2;
    }

    .b24-page-subtitle {
        margin-top: 6px;
        font-size: 13px;
        color: #6f737a;
        line-height: 1.4;
        max-width: 560px;
    }

    .b24-header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .b24-btn {
        background: #2fc6f6;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 8px 14px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background .2s;
        white-space: nowrap;
    }

    .b24-btn:hover { background: #25b5e4; }

    .b24-btn--secondary {
        background: #ffffff;
        color: #2f343b;
        border: 1px solid #cfd4d9;
    }

    .b24-btn--secondary:hover {
        background: #f1f3f5;
        border-color: #c4c9cf;
    }

    .b24-btn--danger {
        background: #ff5752;
    }

    .b24-btn--danger:hover {
        background: #f14b46;
    }

    .b24-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .b24-section {
        margin-bottom: 16px;
    }

    .b24-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .b24-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #2f343b;
        margin: 0;
    }

    .b24-section-hint {
        font-size: 12px;
        color: #8a8f98;
        margin-top: 6px;
        line-height: 1.4;
    }

    /* --- Status card --- */
    .b24-status-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-top: 6px;
    }

    .b24-status-row {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #2f343b;
    }

    .b24-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
        background: #cfd4d9;
    }

    .b24-dot--ok { background: #4bb34b; }
    .b24-dot--warn { background: #ffb100; }
    .b24-dot--err { background: #ff5752; }
    .b24-dot--info { background: #2fc6f6; }

    .b24-status-actions {
        display: flex;
        gap: 10px;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    /* --- Jobs table --- */
    .b24-table-wrap {
        overflow: auto;
        border: 1px solid #e7eaee;
        border-radius: 10px;
    }

    .b24-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 720px;
        background: #fff;
    }

    .b24-table thead th {
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6f737a;
        padding: 12px 12px;
        background: #fafbfc;
        border-bottom: 1px solid #e7eaee;
        white-space: nowrap;
    }

    .b24-table tbody td {
        font-size: 13px;
        color: #2f343b;
        padding: 12px 12px;
        border-bottom: 1px solid #f0f2f4;
        vertical-align: middle;
    }

    .b24-table tbody tr:hover td {
        background: #fcfdff;
    }

    .b24-job-title {
        font-weight: 600;
        color: #2f343b;
        line-height: 1.2;
    }

    .b24-job-meta {
        font-size: 12px;
        color: #8a8f98;
        margin-top: 4px;
        white-space: nowrap;
    }

    .b24-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid #e7eaee;
        background: #fff;
        white-space: nowrap;
    }

    .b24-badge--running { border-color: rgba(47,198,246,.35); background: rgba(47,198,246,.10); }
    .b24-badge--paused { border-color: rgba(255,177,0,.35); background: rgba(255,177,0,.10); }
    .b24-badge--done { border-color: rgba(75,179,75,.35); background: rgba(75,179,75,.10); }
    .b24-badge--failed { border-color: rgba(255,87,82,.35); background: rgba(255,87,82,.10); }
    .b24-badge--draft { border-color: rgba(207,212,217,.8); background: rgba(207,212,217,.18); }

    .b24-progress {
        height: 8px;
        border-radius: 999px;
        background: #eef2f5;
        overflow: hidden;
        min-width: 120px;
    }

    .b24-progress > span {
        display: block;
        height: 100%;
        width: 0;
        background: #2fc6f6;
        border-radius: 999px;
    }

    .b24-actions-inline {
        display: inline-flex;
        gap: 8px;
        align-items: center;
        white-space: nowrap;
    }

    .b24-icon-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: 1px solid #e7eaee;
        background: #fff;
        cursor: pointer;
        transition: background .15s, border-color .15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #2f343b;
    }

    .b24-icon-btn:hover {
        background: #f6f8fa;
        border-color: #dfe3e8;
    }

    /* --- Empty state --- */
    .b24-empty {
        padding: 22px;
        border: 1px dashed #dfe3e8;
        border-radius: 12px;
        background: #fff;
    }

    .b24-empty-title {
        font-size: 15px;
        font-weight: 700;
        color: #2f343b;
        margin: 0 0 8px 0;
    }

    .b24-empty-text {
        font-size: 13px;
        color: #6f737a;
        line-height: 1.5;
        margin: 0 0 14px 0;
        max-width: 560px;
    }

    /* --- Settings (твой блок, почти без изменений) --- */
    .b24-settings-section { background: #f5f7f8; }

    .b24-settings-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .b24-settings-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 14px;
        color: #333;
    }

    .b24-settings-head{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        margin-bottom: 14px;
    }

    .b24-close-btn{
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #e7eaee;
        background: #fff;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        color:#6f737a;
        transition: background .15s, border-color .15s;
    }

    .b24-close-btn:hover{
        background:#f6f8fa;
        border-color:#dfe3e8;
    }

    .b24-close-btn:disabled{
        opacity: .4;
        cursor: not-allowed;
    }

    .b24-form-group { margin-bottom: 14px; }

    .b24-form-label {
        display: block;
        font-size: 13px;
        margin-bottom: 6px;
        color: #555;
    }

    .b24-form-label.b24-required::before {
        content: '*';
        color: #ff5752;
        font-weight: 600;
        margin-right: 2px;
    }

    .b24-input {
        width: 100%;
        height: 38px;
        padding: 0 10px;
        border: 1px solid #cfd4d9;
        border-radius: 4px;
        color: #2f343b;
        font-weight: 400;
        font-size: 14px;
        transition: border-color .2s, box-shadow .2s;
    }

    .b24-input:focus {
        outline: none;
        border-color: #2fc6f6;
        box-shadow: 0 0 0 2px rgba(47, 198, 246, 0.2);
    }

    .b24-input::placeholder { color: #9aa1ab; font-weight: 400; }

    .b24-form-hint {
        margin-top: 8px;
        font-size: 12px;
        line-height: 1.4;
        color: #8a8f98;
    }

    .b24-form-hint a {
        color: #2067b0;
        text-decoration: none;
        border-bottom: 1px solid rgba(32, 103, 176, 0.3);
        transition: color .15s ease, border-color .15s ease;
    }

    .b24-form-hint a:hover {
        color: #1a5aa0;
        border-bottom-color: rgba(32, 103, 176, 0.6);
    }

    .b24-path { color: #6f737a; font-size: 12px; white-space: nowrap; }
    .b24-path-sep { margin: 0 4px; color: #b0b4bb; }

    .b24-field-name {
        background: #f1f3f5;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 12px;
        color: #555;
        white-space: nowrap;
    }

    .b24-chip {
        display: inline-block;
        background: #e8f0fe;
        color: #1a5aa0;
        padding: 1px 6px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 500;
    }

    .b24-save-status {
        font-size: 13px;
        color: #4bb34b;
        display: none;
        padding-top: 8px;
    }

    /* ===============================
   Disabled buttons (Bitrix24-like)
   =============================== */

    .b24-btn:disabled,
    .b24-btn[disabled] {
        background: #e6eaef;
        color: #9aa1ab;
        cursor: auto;
        box-shadow: none;
        opacity: 1; /* важно — Bitrix не использует opacity */
    }

    .b24-btn:disabled:hover,
    .b24-btn[disabled]:hover {
        background: #e6eaef;
        color: #9aa1ab;
    }

    /* Secondary button */
    .b24-btn--secondary:disabled,
    .b24-btn--secondary[disabled] {
        background: #f5f7f8;
        color: #9aa1ab;
        border-color: #dfe3e8;
        cursor: auto;
    }

    .b24-btn--secondary:disabled:hover,
    .b24-btn--secondary[disabled]:hover {
        background: #f5f7f8;
    }

    /* Icon buttons (если будут disabled) */
    .b24-icon-btn:disabled,
    .b24-icon-btn[disabled] {
        background: #f5f7f8;
        color: #b0b4bb;
        border-color: #e7eaee;
        cursor: auto;
    }

    .b24-icon-btn:disabled:hover {
        background: #f5f7f8;
    }

    /* ===============================
   Settings smooth collapse/expand
   =============================== */

    .b24-collapsible {
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transform: translateY(-6px);
        transition:
                max-height 280ms ease,
                opacity 180ms ease,
                transform 180ms ease;
        will-change: max-height, opacity, transform;
        margin-bottom: 0;
    }

    .b24-collapsible.is-open {
        max-height: 1200px; /* достаточно для формы и подсказок */
        opacity: 1;
        transform: translateY(0);
        margin-bottom: 16px;
    }

    .b24-collapsible.is-close {
        margin-bottom: 0;
    }

    @media (prefers-reduced-motion: reduce) {
        .b24-collapsible {
            transition: none;
        }
    }

</style>

<div class="b24-app-layout">

    <!-- MAIN -->
    <div class="b24-app-main">

        <!-- Header -->
        <div class="b24-page-header">
            <div>
                <h1 class="b24-page-title">Мигратор данных</h1>
                <div class="b24-page-subtitle">
                    Перенос CRM-сущностей, пользователей и каталога между порталами Bitrix24.
                    Создавайте задания, запускайте миграцию и отслеживайте прогресс — без ручных перезапусков.
                </div>
            </div>

<!--            <div class="b24-header-actions">-->
<!--                <button class="b24-btn b24-btn--secondary" type="button" onclick="openDocs()">-->
<!--                    Документация-->
<!--                </button>-->
<!--                <button class="b24-btn b24-btn--secondary" type="button" onclick="openSupport()">-->
<!--                    Помощь-->
<!--                </button>-->
<!--            </div>-->
        </div>

        <?php
        $webhookConfigured = !empty($client['webhook']);
        ?>

        <!-- Status -->
        <div class="b24-section">
            <div class="b24-card">
                <div class="b24-section-head">
                    <h2 class="b24-section-title">Состояние</h2>
                </div>

                <div class="b24-status-grid">
                    <div class="b24-status-row">
                        <span class="b24-dot b24-dot--ok"></span>
                        <span><strong>Портал (текущий):</strong> <?= $domain ?: '—' ?></span>
                    </div>
                    <div class="b24-status-row">
                        <span class="b24-dot <?= !empty($client['webhook']) ? 'b24-dot--ok' : 'b24-dot--warn' ?>"></span>
                        <span>
                            <strong>Вебхук:</strong>
                            <?= !empty($client['webhook']) ? 'настроен' : 'не указан (задания создавать нельзя)' ?>
                        </span>
                    </div>
                    <div class="b24-status-row">
                        <span class="b24-dot b24-dot--info"></span>
                        <span><strong>Активных миграций:</strong> <span id="active-jobs-count">0</span></span>
                    </div>
                </div>

                <div class="b24-status-actions">
                    <button
                            class="b24-btn b24-btn--secondary"
                            type="button"
                            onclick="toggleSettings()"
                        <?= $webhookConfigured ? '' : 'disabled title="Сначала заполните вебхук ниже"' ?>
                    >
                        Настройки
                    </button>
                    <button class="b24-btn" type="button" onclick="createJob()" <?= $webhookConfigured ? '' : 'disabled' ?>>
                        + Создать миграцию
                    </button>
                </div>

                <?php if (empty($client['webhook'])): ?>
                    <div class="b24-section-hint">
                        Подсказка: чтобы создавать и запускать миграции, сначала укажи вебхук в настройках справа.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Settings (toggle / auto-open) -->
        <div class="b24-section b24-collapsible <?= $webhookConfigured ? '' : 'is-open' ?>" id="settings-section">
            <div class="b24-settings-card">
                <div class="b24-settings-head">
                    <div class="b24-settings-title">Настройки</div>

                    <!-- Кнопка закрытия -->
                    <button type="button"
                            class="b24-close-btn"
                            aria-label="Закрыть настройки"
                            onclick="closeSettings()"
                        <?= $webhookConfigured ? '' : 'disabled title="Сначала заполните настройки"' ?>>
                        ✕
                    </button>
                </div>

                <form id="settings-form">
                    <input type="hidden" name="domain" value="<?= $domain ?>" />

                    <div class="b24-form-group">
                        <label for="webhook" class="b24-form-label b24-required">Ссылка на вебхук:</label>
                        <input
                                id="webhook"
                                class="b24-input"
                                type="text"
                                name="webhook"
                                placeholder="Скопируйте в поле ссылку на входящий вебхук"
                                value="<?= htmlspecialchars($client['webhook'] ?? '') ?>"
                                data-initial-value="<?= htmlspecialchars($client['webhook'] ?? '') ?>"
                                required
                        />
                        <div class="b24-form-hint">
                            Создайте входящий вебхук в разделе
                            <span class="b24-path">
                        Разработчикам
                        <span class="b24-path-sep">›</span>
                        <a href="https://<?= $domain ?>/devops/section/standard/" target="_blank">Другое</a>
                    </span>.
                            Установите права <span class="b24-chip">CRM (crm)</span>, <span class="b24-chip">Пользователи (user)</span>,
                            <span class="b24-chip">Торговый каталог (catalog)</span>.
                            Скопируйте значение поля <span class="b24-field-name">Вебхук для вызова REST API</span>
                        </div>
                    </div>

                    <div class="b24-status-actions">
                        <button type="submit" class="b24-btn" id="save-btn" disabled>Сохранить</button>
                        <span id="save-status" class="b24-save-status">Сохранено</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Jobs -->
        <div class="b24-section">
            <div class="b24-card">
                <div class="b24-section-head">
                    <div>
                        <h2 class="b24-section-title">Задания на миграцию</h2>
                        <div class="b24-section-hint">
                            Здесь отображаются все задания: черновики, активные миграции, завершённые и с ошибками.
                        </div>
                    </div>

                    <div class="b24-header-actions">
                        <button class="b24-btn" type="button" onclick="createJob()" <?= empty($client['webhook']) ? 'disabled' : '' ?>>
                            + Создать миграцию
                        </button>
                    </div>
                </div>

                <?php if (empty($jobs)): ?>
                    <div class="b24-empty">
                        <div class="b24-empty-title">Пока нет заданий на миграцию</div>
                        <p class="b24-empty-text">
                            Создай первое задание — выбери сущности, задай фильтры и запусти перенос.
                            Миграция будет выполняться пакетами с возможностью паузы и продолжения.
                        </p>
                        <button class="b24-btn" type="button" onclick="createJob()" <?= empty($client['webhook']) ? 'disabled' : '' ?>>
                            Создать первую миграцию
                        </button>
                        <?php if (empty($client['webhook'])): ?>
                            <div class="b24-section-hint" style="margin-top:10px;">
                                Кнопка станет активной после заполнения вебхука в настройках.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="b24-table-wrap">
                        <table class="b24-table">
                            <thead>
                            <tr>
                                <th>Задание</th>
                                <th>Источник → Приёмник</th>
                                <th>Сущности</th>
                                <th>Статус</th>
                                <th>Прогресс</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <?php
                                $status = $job['status'] ?? 'DRAFT';
                                $progress = (int)($job['progress'] ?? 0);

                                $badgeClass = 'b24-badge--draft';
                                $badgeText = 'Черновик';

                                if ($status === 'RUNNING') { $badgeClass = 'b24-badge--running'; $badgeText = 'Выполняется'; }
                                elseif ($status === 'PAUSED') { $badgeClass = 'b24-badge--paused'; $badgeText = 'Пауза'; }
                                elseif ($status === 'DONE') { $badgeClass = 'b24-badge--done'; $badgeText = 'Завершено'; }
                                elseif ($status === 'FAILED') { $badgeClass = 'b24-badge--failed'; $badgeText = 'Ошибка'; }
                                elseif ($status === 'READY') { $badgeClass = 'b24-badge--running'; $badgeText = 'Готово к запуску'; }
                                ?>
                                <tr>
                                    <td>
                                        <div class="b24-job-title"><?= htmlspecialchars($job['title'] ?? 'Без названия') ?></div>
                                        <div class="b24-job-meta">Обновлено: <?= htmlspecialchars($job['updated'] ?? '—') ?></div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($job['source'] ?? '—') ?> → <?= htmlspecialchars($job['target'] ?? '—') ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(implode(', ', $job['entities'] ?? [])) ?>
                                    </td>
                                    <td>
                                        <span class="b24-badge <?= $badgeClass ?>">
                                            <span class="b24-dot <?= ($status==='FAILED')?'b24-dot--err':(($status==='DONE')?'b24-dot--ok':(($status==='PAUSED')?'b24-dot--warn':(($status==='RUNNING')?'b24-dot--info':'b24-dot--warn'))) ?>"></span>
                                            <?= $badgeText ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="b24-progress" title="<?= $progress ?>%">
                                            <span style="width: <?= $progress ?>%"></span>
                                        </div>
                                        <div class="b24-job-meta"><?= $progress ?>%</div>
                                    </td>
                                    <td>
                                        <div class="b24-actions-inline">
                                            <button class="b24-icon-btn" type="button" title="Открыть" onclick="openJob('<?= htmlspecialchars($job['id'] ?? '') ?>')">▶</button>
                                            <button class="b24-icon-btn" type="button" title="Пауза" onclick="pauseJob('<?= htmlspecialchars($job['id'] ?? '') ?>')">⏸</button>
                                            <button class="b24-icon-btn" type="button" title="Логи" onclick="openLogs('<?= htmlspecialchars($job['id'] ?? '') ?>')">📄</button>
                                            <button class="b24-icon-btn" type="button" title="Остановить" onclick="stopJob('<?= htmlspecialchars($job['id'] ?? '') ?>')">⛔</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

</div>

<script>
    const webhookConfigured = <?= $webhookConfigured ? 'true' : 'false' ?>;

    function getSettingsEl() {
        return document.getElementById('settings-section');
    }

    function isSettingsOpen(el) {
        return el.classList.contains('is-open');
    }

    function openSettings() {
        const el = getSettingsEl();
        if (!el) return;

        el.classList.add('is-open');

        // лёгкий UX: прокрутка только когда пользователь сам открывает
        // (если автопоказ при пустом вебхуке — можно не скроллить)
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function openSettingsSilently() {
        const el = getSettingsEl();
        if (!el) return;
        el.classList.add('is-open');
    }

    function closeSettings() {
        if (!webhookConfigured) return; // по твоим правилам
        const el = getSettingsEl();
        if (!el) return;

        el.classList.remove('is-open');
    }

    function toggleSettings() {
        if (!webhookConfigured) return;
        const el = getSettingsEl();
        if (!el) return;

        if (isSettingsOpen(el)) closeSettings();
        else openSettings();
    }

    // ----------------------------
    // ЛОГИКА КНОПКИ "СОХРАНИТЬ"
    // ----------------------------
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('settings-form');
        const saveBtn = document.getElementById('save-btn');
        const webhookInput = document.getElementById('webhook');
        const status = document.getElementById('save-status');

        // ВАЖНО: initialValue должен быть изменяемым
        let initialValue = (webhookInput.dataset.initialValue || '').trim();

        function getCurrentValue() {
            return (webhookInput.value || '').trim();
        }

        function updateSaveButtonState() {
            const currentValue = getCurrentValue();

            const isNotEmpty = currentValue.length > 0;
            const isChanged = currentValue !== initialValue;

            saveBtn.disabled = !(isNotEmpty && isChanged);
        }

        // Начальное состояние
        saveBtn.disabled = true;
        updateSaveButtonState();

        webhookInput.addEventListener('input', updateSaveButtonState);
        webhookInput.addEventListener('change', updateSaveButtonState);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (saveBtn.disabled) return;

            const currentValue = getCurrentValue();
            if (!currentValue) {
                updateSaveButtonState();
                return;
            }

            // защита от двойного клика
            saveBtn.disabled = true;

            try {
                const response = await fetch('/app-settings/update', {
                    method: 'POST',
                    body: new FormData(form),
                });

                const result = await response.json();

                if (result.status === 'OK') {
                    initialValue = currentValue;
                    webhookInput.dataset.initialValue = currentValue;

                    status.style.display = 'inline';
                    setTimeout(() => status.style.display = 'none', 2000);

                    updateSaveButtonState();

                    document.querySelectorAll('button[onclick="createJob()"]').forEach(btn => btn.disabled = false);

                    const settingsBtn = document.querySelector('button[onclick="toggleSettings()"]');
                    if (settingsBtn) {
                        settingsBtn.disabled = false;
                        settingsBtn.removeAttribute('title');
                    }
                } else {
                    alert('Ошибка: ' + (result.error || 'Неизвестная ошибка'));
                    webhookInput.value = initialValue;
                    updateSaveButtonState();
                }
            } catch (err) {
                alert('Ошибка: ' + err.message);
                updateSaveButtonState();
            }
        });
    });

    function createJob() {
        alert('Открыть мастер создания миграции (TODO)');
    }
</script>
