<?php 
function getConnection() {
    $DB_DSN = "sqlsrv:server=tcp:guardian-dev-db.database.windows.net,1433;Database=GUARDIAN-DEV";
    $DB_USER = "GUARDIAN";
    $DB_PASSWORD = "Sh13ldlyt1c$";
    try {
        $conn = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Error connecting to SQL Server: " . $e->getMessage());
    }
}

if(isset($_POST["method"])) {
    $method = $_POST["method"];
    if($method == "getByRiskLevel" && isset($_POST["riskLevel"])) {
        getByRiskLevel($_POST["riskLevel"]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["error" => "Invalid method or missing riskLevel"]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid method"]);
}

function getByRiskLevel($risklevel) {
    $pdo = getConnection(); // Assume this returns a PDO connection

    $sql = "WITH RatingScores AS (
        SELECT VENDOR_NAME,
               COUNT(*) AS RATING_SCORE,
               CASE
                   WHEN COUNT(*) >= 35 THEN 'TOP'
                   WHEN COUNT(*) BETWEEN 25 AND 34 THEN 'HIGH'
                   WHEN COUNT(*) BETWEEN 15 AND 24 THEN 'MODERATE'
                   WHEN COUNT(*) <= 14 THEN 'LOW'
               END AS score_category
        FROM DBO.VENDOR_DETAILS
        GROUP BY VENDOR_NAME
    ),
    ProductDiversity AS (
        SELECT VENDOR_NAME,
               COUNT(DISTINCT PRODUCT_NAME) AS Product_Diversity_Score,
               MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Verified_Company_Score,
               COUNT(DISTINCT PRODUCT_NAME) + MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Total_Score
        FROM DBO.VENDOR_DETAILS
        GROUP BY VENDOR_NAME
    )
    SELECT vd.VENDOR_ID,
           vd.VENDOR_NAME,
           -- Include other fields as needed
           COALESCE(r.RATING_SCORE, 0) AS RATING_SCORE,
           r.score_category,
           COALESCE(pd.Product_Diversity_Score, 0) AS Product_Diversity_Score,
           COALESCE(pd.Verified_Company_Score, 0) AS Verified_Company_Score,
           COALESCE(pd.Total_Score, 0) AS Total_Score
    FROM VENDOR_DETAILS vd
    LEFT JOIN RatingScores r ON vd.VENDOR_NAME = r.VENDOR_NAME
    LEFT JOIN ProductDiversity pd ON vd.VENDOR_NAME = pd.VENDOR_NAME
    WHERE r.score_category = :risklevel
    ORDER BY RATING_SCORE DESC, Total_Score DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':riskLevel', $riskLevel, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    $json = json_encode(array('items' => $results));
    echo $json;
}

?>