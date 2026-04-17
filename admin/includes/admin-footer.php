</div>

<style>
.admin-nav {
    background: #2c3e50;
    color: white;
    padding: 15px 0;
    margin-bottom: 30px;
}

.admin-nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

.admin-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
}

.breadcrumb-link {
    color: #ecf0f1;
    text-decoration: none;
    transition: color 0.3s;
}

.breadcrumb-link:hover {
    color: #3498db;
}

.breadcrumb-separator {
    color: #95a5a6;
}

.breadcrumb-current {
    color: #ecf0f1;
    font-weight: 500;
}

.admin-menu {
    flex: 1;
    display: flex;
    justify-content: center;
}

.admin-nav-links {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 10px;
}

.admin-nav-links a {
    color: #ecf0f1;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 5px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 5px;
}

.admin-nav-links a:hover {
    background: #34495e;
    color: #3498db;
}

.admin-nav-links a.active {
    background: #3498db;
    color: white;
}

.admin-user {
    display: flex;
    align-items: center;
    gap: 15px;
}

.admin-welcome {
    color: #ecf0f1;
    font-size: 0.9em;
}

.admin-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.85em;
}

.btn-danger {
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-danger:hover {
    background: #c0392b;
}

@media (max-width: 768px) {
    .admin-nav-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .admin-menu {
        order: 3;
    }
    
    .admin-nav-links {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .admin-user {
        justify-content: space-between;
        order: 2;
    }
    
    .admin-breadcrumb {
        order: 1;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
