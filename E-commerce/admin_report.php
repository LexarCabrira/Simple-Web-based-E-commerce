<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include('includes/db_connect.php');

// 1. FETCH SUMMARY DATA
$sales_res = mysqli_query($conn, "SELECT SUM(total_price) as total, COUNT(*) as count FROM orders WHERE status='Delivered'");
$sales_data = mysqli_fetch_assoc($sales_res);

// 2. FETCH TRANSACTION LIST
$transactions = mysqli_query($conn, "SELECT * FROM orders WHERE status='Delivered' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Silverlocks | Sales Report</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 50px; color: #333; }
        .receipt-container { max-width: 800px; margin: 0 auto; border: 1px solid #eee; padding: 40px; }
        
        .header { text-align: center; margin-bottom: 50px; }
        .header h1 { font-family: 'serif'; letter-spacing: 4px; margin-bottom: 5px; text-transform: uppercase; }
        .header p { margin: 0; color: #777; font-size: 0.9rem; }

        .meta-info { display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 0.9rem; border-bottom: 2px solid #333; padding-bottom: 10px; }
        
        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .report-table th { text-align: left; text-transform: uppercase; font-size: 0.75rem; border-bottom: 1px solid #333; padding: 10px 5px; }
        .report-table td { padding: 15px 5px; border-bottom: 1px solid #eee; font-size: 0.9rem; }

        .summary-box { text-align: right; margin-top: 20px; }
        .summary-line { font-size: 1rem; margin-bottom: 5px; }
        .total-amount { font-size: 1.5rem; font-weight: bold; margin-top: 10px; border-top: 2px solid #333; display: inline-block; padding-top: 10px; }

        .footer { text-align: center; margin-top: 80px; font-size: 0.8rem; color: #999; border-top: 1px dashed #ccc; pt: 20px; }

        /* Print Trigger */
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .receipt-container { border: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Close</button>
    </div>

    <div class="receipt-container">
        <div class="header">
            <h1>Silverlocks</h1>
            <p>Official Sales Summary Report</p>
            <p>Artisanal Bakery & Confections</p>
        </div>

        <div class="meta-info">
            <div>
                <strong>Generated On:</strong> <?php echo date("M d, Y h:i A"); ?><br>
                <strong>Report Period:</strong> Lifetime (Delivered)
            </div>
            <div style="text-align: right;">
                <strong>Transactions:</strong> <?php echo $sales_data['count']; ?> Items
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Tracking ID</th>
                    <th>Customer</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($transactions)): ?>
                <tr>
                    <td><?php echo date("M d, Y", strtotime($row['created_at'])); ?></td>
                    <td style="font-family: monospace; font-weight: bold;"><?php echo $row['tracking_no']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td style="text-align: right;">₱<?php echo number_format($row['total_price'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="summary-box">
            <div class="summary-line">Items Fulfilled: <strong><?php echo $sales_data['count']; ?></strong></div>
            <div class="total-amount">TOTAL REVENUE: ₱<?php echo number_format($sales_data['total'], 2); ?></div>
        </div>

        <div class="footer">
            <p>THANK YOU FOR YOUR HARD WORK!</p>
            <p>This is a computer-generated report and does not require a physical signature.</p>
        </div>
    </div>

</body>
</html>