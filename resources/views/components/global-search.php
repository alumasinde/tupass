<?php
$actionUrl = htmlspecialchars($action ?? '', ENT_QUOTES, 'UTF-8');
$currentQuery = trim($_GET['q'] ?? '');

// Preserve other query parameters except "q"
$queryParams = $_GET ?? [];
unset($queryParams['q']);
?>

<form method="GET" action="<?= $actionUrl ?>" class="global-search" role="search">

    <?php foreach ($queryParams as $key => $value): ?>
        <?php if (is_scalar($value)): ?>
            <input type="hidden"
                   name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                   value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="search-group">
        <input
            type="text"
            name="q"
            value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>"
            placeholder="Search..."
            class="search-input"
            id="searchInput"
            autocomplete="off"
        >

        <button type="submit" class="search-btn">
            Search
        </button>

        <?php if ($currentQuery !== ''): ?>
            <a href="<?= $actionUrl ?>" class="clear-btn">
                Clear
            </a>
        <?php endif; ?>
    </div>

</form>

<style>
.global-search {
    display: flex;
    justify-content: left;
    margin: 20px 0;
}
.global-search .search-group {
    display: flex;
    width: 100%;
    max-width: 600px;
}
.global-search .search-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid var(--gray);
    border-radius: 4px 0 0 4px;
    font-size: 16px;
}
.global-search .search-btn {
    padding: 8px 16px;
    border: none;
    background: var(--primary);
    color: white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 0 4px 4px 0;
}
.global-search .search-btn:hover {
    background: #2563eb;
}
.global-search .clear-btn {
    margin-left: 10px;
    padding: 8px 16px;
    border: none;
    background: var(--gray);
    color: white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
}
.global-search .clear-btn:hover {
    background: #04090e;
}
</style>