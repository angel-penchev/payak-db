<?php
// Since this is included from index.php, $pdo is already available from config/db.php
// and $projectId is already defined by the regex matcher.

$project = null;
$members = [];

try {
    if (isset($projectId)) {
        // 1. Вземане на детайли за проекта
        $stmt = $pdo->prepare("SELECT * FROM group_projects WHERE id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($project) {
            // 2. Вземане на членовете чрез свързване с таблицата за потребители
            // Използваме CONCAT, за да съберем first_name и last_name
            $stmt_members = $pdo->prepare("
                SELECT 
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name, 
                    u.avatar_url 
                FROM group_project_members gpm
                JOIN users u ON gpm.student_id = u.id
                WHERE gpm.group_project_id = ?
            ");
            $stmt_members->execute([$projectId]);
            $members = $stmt_members->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    echo "Грешка при зареждане: " . $e->getMessage();
}

if (!$project): ?>
    <div style="text-align:center; padding: 50px;">
        <h2>Проектът не е намерен</h2>
        <a href="<?= BASE_URL ?>/courses/<?= htmlspecialchars($courseId) ?>">Назад към курса</a>
    </div>
<?php return; endif; ?>

<style>
    .project-container {
        max-width: 900px;
        margin: 20px auto;
        color: #000;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- Top Header Box (Matches Sketch) --- */
    .header-box {
        border: 2px solid #000;
        padding: 40px 20px;
        text-align: center;
        position: relative;
        margin-bottom: 40px;
    }
    .header-box h1 {
        font-size: 2.8rem;
        margin: 0 0 20px 0;
        font-weight: 500;
    }
    .avatar-row {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .avatar-circle {
        width: 45px;
        height: 45px;
        border: 2px solid #000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9f9f9;
        font-size: 1.2rem;
    }
    .edit-icon {
        position: absolute;
        bottom: 10px;
        right: 10px;
        color: #3498db;
        border: 2px solid #3498db;
        padding: 4px 8px;
        text-decoration: none;
        font-size: 0.9rem;
        border-radius: 4px;
    }

    /* --- Content Sections --- */
    .detail-section { margin-bottom: 30px; }
    .label {
        display: block;
        font-weight: bold;
        font-size: 1.3rem;
        margin-bottom: 8px;
    }
    .text-content {
        font-size: 1.1rem;
        line-height: 1.5;
        white-space: pre-wrap;
    }

    /* --- Links Row --- */
    .links-row {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .web-link-box {
        border: 2px solid #000;
        padding: 8px 15px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .download-btn {
        border: 2px solid #f1c40f;
        color: #f39c12;
        padding: 8px 20px;
        text-decoration: none;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* --- Created By List --- */
    .creator-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 15px;
    }
</style>

<div class="project-container">
    <div class="header-box">
        <h1><?= htmlspecialchars($project['name']) ?></h1>
        <div class="avatar-row">
            <?php foreach ($members as $m): ?>
                <div class="avatar-circle">👤</div>
            <?php endforeach; ?>
        </div>
        <a href="#" class="edit-icon">✎</a>
    </div>

    <div class="detail-section">
        <span class="label">Тема</span>
        <div class="text-content"><?= htmlspecialchars($project['topic']) ?></div>
    </div>

    <div class="detail-section">
        <span class="label">Описание</span>
        <div class="text-content"><?= htmlspecialchars($project['description'] ?? 'Няма въведено описание за този проект.') ?></div>
    </div>

    <div class="detail-section">
        <span class="label">Линкове</span>
        <div class="links-row">
            <div class="web-link-box">
                🌐 <?= htmlspecialchars($project['name']) ?>
            </div>
            <a href="#" class="download-btn">
                <span>↓</span> Изтегляне на код
            </a>
        </div>
    </div>

    <div class="detail-section">
        <span class="label">Създаден от</span>
        <?php foreach ($members as $member): ?>
            <div class="creator-item">
                <div class="avatar-circle">
                    <?php if (!empty($member['avatar_url'])): ?>
                        <img src="<?= htmlspecialchars($member['avatar_url']) ?>" alt="Avatar" style="width:100%; height:100%; border-radius:50%;">
                    <?php else: ?>
                        👤
                    <?php endif; ?>
                </div>
                <span><?= htmlspecialchars($member['full_name']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>