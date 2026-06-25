<?php
session_start();
include("connect.php");

// Safety check: ensure user is logged in
if(!isset($_SESSION['username'])){
    exit("Unauthorized access");
}

// Get user role for conditional action buttons
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'employee';

// Capture the search query string
$search = isset($_POST['query']) ? trim($_POST['query']) : '';

if ($search !== '') {
    // Search by ID, Full Name, Email, Department, or Position using wildcards
    $search_term = "%" . $search . "%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM details WHERE id LIKE ? OR fullname LIKE ? OR email LIKE ? OR department LIKE ? OR position LIKE ?");
    mysqli_stmt_bind_param($stmt, "sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // If search box is empty, pull all records
    $result = mysqli_query($conn, "SELECT * FROM details");
}

// Generate rows dynamically
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><strong>#<?php echo $row['id']; ?></strong></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['department']); ?></td>
            <td><?php echo htmlspecialchars($row['position']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['salary']); ?></td>
            <td>
                <?php if($user_role == 'super_admin'){ ?>
                    <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                        <i class="fa-solid fa-user-pen"></i> Edit
                    </a>
                <?php } ?>
                <a href="delete_employee.php?id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this employee?');">
                    <i class="fa-solid fa-trash-can"></i> Delete
                </a>
            </td>
        </tr>
    <?php }
} else { ?>
    <tr>
        <td colspan="8" style="text-align: center; padding: 20px; color: #6b7280;">
            <i class="fa-solid fa-magnifying-glass-blur" style="font-size: 1.5rem; margin-bottom: 8px; display: block;"></i>
            No matching employee records found.
        </td>
    </tr>
<?php } ?>