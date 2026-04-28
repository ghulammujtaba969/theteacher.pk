<?php
/**
 * Database Migration Script - Multiple File Formats Support
 * Run this script to add support for multiple file formats per lecture
 */

// Database configuration
$host = 'sdb-86.hosting.stackcp.net';
$dbname = 'syllabusms-353130306bcf';
$username = 'syllabusms-353130306bcf';
$password = '4ndqnhreo6';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
      echo "╔════════════════════════════════════════════════╗\n";
    echo "║     DATABASE STRUCTURE INSPECTOR              ║\n";
    echo "║     Database: " . str_pad($dbname, 32) . "║\n";
    echo "╚════════════════════════════════════════════════╝\n\n";
    
    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📊 Total Tables: " . count($tables) . "\n\n";
    
    foreach ($tables as $index => $table) {
        echo "┌─────────────────────────────────────────────\n";
        echo "│ " . ($index + 1) . ". TABLE: $table\n";
        echo "└─────────────────────────────────────────────\n\n";
        
        // Columns
        $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        echo "  📋 COLUMNS (" . count($columns) . "):\n";
        foreach ($columns as $col) {
            $info = "    ├─ {$col['Field']}";
            $info .= " → {$col['Type']}";
            
            $flags = [];
            if ($col['Key'] == 'PRI') $flags[] = '🔑 PRIMARY';
            if ($col['Key'] == 'MUL') $flags[] = '🔗 INDEX';
            if ($col['Key'] == 'UNI') $flags[] = '⭐ UNIQUE';
            if ($col['Null'] == 'NO') $flags[] = '❗ NOT NULL';
            if ($col['Extra']) $flags[] = strtoupper($col['Extra']);
            if ($col['Default'] !== null) $flags[] = "DEFAULT: {$col['Default']}";
            
            if (!empty($flags)) {
                $info .= " [" . implode(", ", $flags) . "]";
            }
            
            echo "$info\n";
        }
        
        // Indexes
        echo "\n  🔍 INDEXES:\n";
        $indexes = $pdo->query("SHOW INDEX FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        $indexGroups = [];
        foreach ($indexes as $idx) {
            $indexGroups[$idx['Key_name']][] = $idx['Column_name'];
        }
        foreach ($indexGroups as $name => $cols) {
            echo "    ├─ $name: " . implode(", ", $cols) . "\n";
        }
        
        // Foreign Keys
        echo "\n  🔗 FOREIGN KEYS:\n";
        $fks = $pdo->query("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = '$dbname'
            AND TABLE_NAME = '$table'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($fks)) {
            echo "    └─ None\n";
        } else {
            foreach ($fks as $fk) {
                echo "    ├─ {$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
            }
        }
        
        // Statistics
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        $stats = $pdo->query("
            SELECT 
                ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb,
                ENGINE,
                TABLE_COLLATION
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = '$dbname' 
            AND TABLE_NAME = '$table'
        ")->fetch(PDO::FETCH_ASSOC);
        
        echo "\n  📈 STATISTICS:\n";
        echo "    ├─ Rows: " . number_format($count) . "\n";
        echo "    ├─ Size: {$stats['size_mb']} MB\n";
        echo "    ├─ Engine: {$stats['ENGINE']}\n";
        echo "    └─ Collation: {$stats['TABLE_COLLATION']}\n";
        
        echo "\n";
    }
    
    // Database summary
    $totalSize = $pdo->query("
        SELECT ROUND(SUM(DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as total_mb
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = '$dbname'
    ")->fetchColumn();
    
    echo "╔════════════════════════════════════════════════╗\n";
    echo "║  📊 DATABASE SUMMARY                          ║\n";
    echo "╠════════════════════════════════════════════════╣\n";
    echo "║  Total Tables: " . str_pad(count($tables), 30) . " ║\n";
    echo "║  Total Size: " . str_pad($totalSize . " MB", 32) . " ║\n";
    echo "╚════════════════════════════════════════════════╝\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>