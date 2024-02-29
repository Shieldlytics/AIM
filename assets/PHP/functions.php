<?php 

// function getConnection() {
//     // $host = "10.214.19.39";
//     // $port = 19307;
//     // $databaseName = "AIM";
//     // $username = "ErnestPenaJr";
//     // $password = "$268RedDragons";
//     // $dsn = "mysql:host=$host;port=$port;dbname=$databaseName;charset=utf8mb4";


//     $DB_DNS = "mysql:host=localhost;dbname=AIM;";
//     $DB_USER = "ErnestPenaJr";
//     $DB_PASSWORD = "$268RedDragons";
//     $conn = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     return $conn;
//     try {
//         $conn = new PDO($dsn, $username, $password);
//         // Set the PDO error mode to exception
//         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
//     } catch(PDOException $e) {
//         echo "Connection failed: " . $e->getMessage();
//     }
//     return $conn;    

// }

// function getConnection() {
//     $DB_DSN = "mysql:host=localhost;dbname=AIM";
//     $DB_USER = "ErnestPenaJr";
//     $DB_PASSWORD = "$268RedDragons";
//     try {
//         $conn = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
//         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         return $conn;
//     } catch (PDOException $e) {
//         // Handle error or log it
//         throw $e; // Or handle it more gracefully
//     }
// }
// function getConnection() {
//     $DB_DNS = "jdbc:sqlserver://;serverName=guardian-dev-db.database.windows.net;databaseName=GUARDIAN-DEV;encrypt=true;trustServerCertificate=false;hostNameInCertificate=*.database.windows.net;loginTimeout=30;";
//     $DB_USER = "GUARDIAN";
//     $DB_PASSWORD = "Sh13ldlyt1c$";
//     $conn = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     return $conn;
// }

function getConnection(){
    $DB_DNS = "mysql:host=10.214.19.39:3307;dbname=AIM;charset=utf8mb4";
    $DB_USER = "ErnestPenaJr";
    $DB_PASSWORD = "$268RedDragons";
    $conn = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

if(isset($_POST["method"])) {
    $method = $_POST["method"];
    if($method=="loadStateSales") {loadStateData();};
    //if the method is getByRiskLevel, then get the risk level from the post request
    if(isset($_POST["riskLevel"])){
        $risklevel = $_POST["riskLevel"];
        if($method=="getByRiskLevel") {getByLevel($risklevel);};
        if($method=="getVendorProductCount") {getVendorProductCount($risklevel);};
    }
    //if the method is getByVendor, then get the vendor name from the post request
    if(isset($_POST["vendorName"])){
        $vendorName = $_POST["vendorName"];
        if($method=="getByVendor") {getByVendor($vendorName);};
        if($method=="getVendorProductCountByName") {getProductCountByName($vendorName);};
    }
    

   
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid method detected"]);
}

function getByLevel($risklevel) {
    $risklevel = !empty($risklevel) ? $risklevel : " ";
    $pdo = getConnection(); 
    if($risklevel == " ") {
        $sql = "SELECT 
        vd.VENDOR_ID,
        vd.VENDOR_NAME,
        vd.PRODUCT_NAME,
        vd.STREET_NAME,
        vd.CITY,
        vd.STATE,
        vd.ZIP_CODE,
        vd.SELLER_FIRST_NAME,
        vd.SELLER_LAST_NAME,
        vd.SELLER_PHONE,
        vd.SELLER_EMAIL,
        vd.SELLER_URL,
        vd.SELLER_NAME_CHANGE,
        vd.ARTICLE_FINDING,
        vd.ARTICLE_URL,
        vd.PRODUCT_GATEGORY,
        vd.ANNUAL_SALES,
        vd.VERIFIED_COMPANY,
        vd.PRICE_DIFFERANCE,
        vd.PRODUCT_PRICE,
        vd.DIFFRENT_ADDRESS,
        vd.WEIGHT,
        COALESCE(R.RATING_SCORE,0) AS RATING_SCORE,
        R.SCORE_CATEGORY,
        COALESCE(pd.Product_Diversity_Score,0) AS Product_Diversity_Score,
        COALESCE(pd.VERIFIED_COMPANY_SCORE,0) AS VERIFIED_COMPANY_SCORE, 
        COALESCE(pd.TOTAL_SCORE,0) AS TOTAL_SCORE 
        FROM VENDOR_DETAILS vd 
        LEFT JOIN (
            SELECT
                VENDOR_NAME,
                CASE
                    WHEN VENDOR_ID IN (3001, 3002, 3003) THEN 100
                    ELSE COUNT(*)
                END AS RATING_SCORE,
                CASE
                    WHEN VENDOR_ID IN (3001, 3002, 3003) THEN 'TOP'
                    WHEN COUNT(*) >= 60 THEN 'TOP'
                    WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH'
                    WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE'
                    WHEN COUNT(*) <= 39 THEN 'LOW'
                END AS SCORE_CATEGORY
            FROM
                VENDOR_DETAILS
            GROUP BY
                VENDOR_NAME
        ) AS R 
        ON vd.VENDOR_NAME = R.VENDOR_NAME 
        LEFT JOIN (
            SELECT VENDOR_NAME,
            COUNT(DISTINCT PRODUCT_NAME) AS 
            PRODUCT_DIVERSITY_SCORE,MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Verified_Company_Score,
            COUNT(DISTINCT PRODUCT_NAME) + MAX(CASE WHEN VERIFIED_COMPANY = 0 THEN 10 ELSE 0 END) AS Total_Score 
            FROM VENDOR_DETAILS GROUP BY VENDOR_NAME
            ) AS pd 
            ON vd.VENDOR_NAME = pd.VENDOR_NAME ORDER BY COALESCE(R.RATING_SCORE, 0) DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $json = json_encode(array('items' => $result));
        echo $json;
        return;
    }else{
        $sql = "SELECT
        VD.VENDOR_ID,
        VD.VENDOR_NAME,
        VD.PRODUCT_NAME,
        VD.STREET_NAME,
        VD.CITY,
        VD.STATE,
        VD.ZIP_CODE,
        VD.SELLER_FIRST_NAME,
        VD.SELLER_LAST_NAME,
        VD.SELLER_PHONE,
        VD.SELLER_EMAIL,
        VD.SELLER_URL,
        VD.SELLER_NAME_CHANGE,
        VD.ARTICLE_FINDING,
        VD.ARTICLE_URL,
        VD.PRODUCT_GATEGORY,
        VD.ANNUAL_SALES,
        VD.VERIFIED_COMPANY,
        VD.PRICE_DIFFERANCE,
        VD.PRODUCT_PRICE,
        VD.DIFFRENT_ADDRESS,
        VD.WEIGHT,
        COALESCE(R.RATING_SCORE,
        0) AS RATING_SCORE,
        R.SCORE_CATEGORY,
        COALESCE(PD.PRODUCT_DIVERSITY_SCORE,
        0) AS PRODUCT_DIVERSITY_SCORE,
        COALESCE(PD.VERIFIED_COMPANY_SCORE,
        0) AS VERIFIED_COMPANY_SCORE,
        COALESCE(PD.TOTAL_SCORE,
        0) AS TOTAL_SCORE
    FROM
        VENDOR_DETAILS VD
        LEFT JOIN (
            SELECT
                VENDOR_NAME,
                CASE
                    WHEN VENDOR_ID IN (3001, 3002, 3003) THEN 100
                    ELSE COUNT(*)
                END AS RATING_SCORE,
                CASE
                    WHEN VENDOR_ID IN (3001, 3002, 3003) THEN 'TOP'
                    WHEN COUNT(*) >= 60 THEN 'TOP'
                    WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH'
                    WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE'
                    WHEN COUNT(*) <= 39 THEN 'LOW'
                END AS SCORE_CATEGORY
            FROM
                VENDOR_DETAILS
            GROUP BY
                VENDOR_NAME
        ) AS R
        ON VD.VENDOR_NAME = R.VENDOR_NAME
        LEFT JOIN (
            SELECT
                VENDOR_NAME,
                COUNT(DISTINCT PRODUCT_NAME) AS PRODUCT_DIVERSITY_SCORE,
                MAX(
                    CASE
                        WHEN VERIFIED_COMPANY = 0 THEN
                            10
                        ELSE
                            0
                    END) AS VERIFIED_COMPANY_SCORE,
                COUNT(DISTINCT PRODUCT_NAME) + MAX(
                    CASE
                        WHEN VERIFIED_COMPANY = 0 THEN
                            10
                        ELSE
                            0
                    END) AS TOTAL_SCORE
            FROM
                VENDOR_DETAILS
            GROUP BY
                VENDOR_NAME
        ) AS PD
        ON VD.VENDOR_NAME = PD.VENDOR_NAME
        WHERE R.SCORE_CATEGORY = :risklevel ORDER BY COALESCE(R.RATING_SCORE, 0) DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':risklevel', $risklevel);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $json = json_encode(array('items' => $result));
        echo $json;
    }
}
function getByVendor($vendorName) {
    $vendorName = !empty($vendorName) ? $vendorName : " ";
    $pdo = getConnection();
    $sql = "SELECT
    VD.VENDOR_ID,
    VD.VENDOR_NAME,
    VD.PRODUCT_NAME,
    VD.STREET_NAME,
    VD.CITY,
    VD.STATE,
    VD.ZIP_CODE,
    VD.SELLER_FIRST_NAME,
    VD.SELLER_LAST_NAME,
    VD.SELLER_PHONE,
    VD.SELLER_EMAIL,
    VD.SELLER_URL,
    VD.SELLER_NAME_CHANGE,
    VD.ARTICLE_FINDING,
    VD.ARTICLE_URL,
    VD.PRODUCT_GATEGORY,
    VD.ANNUAL_SALES,
    VD.VERIFIED_COMPANY,
    VD.PRICE_DIFFERANCE,
    VD.PRODUCT_PRICE,
    VD.DIFFRENT_ADDRESS,
    VD.WEIGHT,
    COALESCE(R.RATING_SCORE,
    0) AS RATING_SCORE,
    R.SCORE_CATEGORY,
    COALESCE(PD.PRODUCT_DIVERSITY_SCORE,
    0) AS PRODUCT_DIVERSITY_SCORE,
    COALESCE(PD.VERIFIED_COMPANY_SCORE,
    0) AS VERIFIED_COMPANY_SCORE,
    COALESCE(PD.TOTAL_SCORE,
    0) AS TOTAL_SCORE
FROM
    VENDOR_DETAILS VD
    LEFT JOIN (
        SELECT
            VENDOR_NAME,
            CASE
                WHEN VENDOR_ID IN (3001, 3002, 3003) THEN 100
                ELSE COUNT(*)
            END AS RATING_SCORE,
            CASE
                WHEN VENDOR_ID IN (3001, 3002, 3003) THEN 'TOP'
                WHEN COUNT(*) >= 60 THEN 'TOP'
                WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH'
                WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE'
                WHEN COUNT(*) <= 39 THEN 'LOW'
            END AS SCORE_CATEGORY
        FROM
            VENDOR_DETAILS
        GROUP BY
            VENDOR_NAME
    ) AS R
    ON VD.VENDOR_NAME = R.VENDOR_NAME
    LEFT JOIN (
        SELECT
            VENDOR_NAME,
            COUNT(DISTINCT PRODUCT_NAME) AS PRODUCT_DIVERSITY_SCORE,
            MAX(
                CASE
                    WHEN VERIFIED_COMPANY = 0 THEN
                        10
                    ELSE
                        0
                END) AS VERIFIED_COMPANY_SCORE,
            COUNT(DISTINCT PRODUCT_NAME) + MAX(
                CASE
                    WHEN VERIFIED_COMPANY = 0 THEN
                        10
                    ELSE
                        0
                END) AS TOTAL_SCORE
        FROM
            VENDOR_DETAILS
        GROUP BY
            VENDOR_NAME
    ) AS PD
    ON VD.VENDOR_NAME = PD.VENDOR_NAME
    WHERE vd.VENDOR_NAME = :vendorName ORDER BY COALESCE(R.RATING_SCORE, 0) DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':vendorName', $vendorName);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array('items' => $result));
    echo $json;
}
function getVendorProductCount($risklevel) {
    $pdo = getConnection(); // Assume this returns a PDO connection
    if($risklevel == " ") {
        $sql = "SELECT R.SCORE_CATEGORY,COUNT(DISTINCT VD.VENDOR_ID) AS DISTINCT_VENDOR_COUNT,COUNT(DISTINCT VD.PRODUCT_NAME) AS DISTINCT_PRODUCT_COUNT FROM VENDOR_DETAILS VD JOIN (SELECT VENDOR_NAME, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS R ON VD.VENDOR_NAME = R.VENDOR_NAME GROUP BY R.SCORE_CATEGORY ORDER BY DISTINCT_VENDOR_COUNT DESC, DISTINCT_PRODUCT_COUNT DESC";
        $stmt = $pdo->prepare($sql);
    }else{
        $sql = "SELECT R.SCORE_CATEGORY,COUNT(DISTINCT VD.VENDOR_ID) AS DISTINCT_VENDOR_COUNT,COUNT(DISTINCT VD.PRODUCT_NAME) AS DISTINCT_PRODUCT_COUNT FROM VENDOR_DETAILS VD JOIN (SELECT VENDOR_NAME, CASE WHEN COUNT(*) >= 60 THEN 'TOP' WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH' WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE' WHEN COUNT(*) <= 39 THEN 'LOW' END AS SCORE_CATEGORY FROM VENDOR_DETAILS GROUP BY VENDOR_NAME) AS R ON VD.VENDOR_NAME = R.VENDOR_NAME WHERE SCORE_CATEGORY = :risklevel GROUP BY R.SCORE_CATEGORY ORDER BY DISTINCT_VENDOR_COUNT DESC, DISTINCT_PRODUCT_COUNT DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':risklevel', $risklevel);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array('items' => $result));
    echo $json;
}
function getProductCountByName($vendorName) {
    $pdo = getConnection();

        $sql = "SELECT 
            COUNT(DISTINCT VD.VENDOR_NAME) AS VENDORS,
            COUNT(DISTINCT VD.PRODUCT_NAME) AS PRODUCTS,
            SUM(VD.ANNUAL_SALES) as ANNUAL_SALES,
            SC.SCORE_CATEGORY,
            COUNT(SC.VENDOR_NAME) AS CATEGORY_COUNT
        FROM 
            VENDOR_DETAILS VD
        LEFT JOIN (
            SELECT
            VENDOR_NAME,
            CASE
                WHEN COUNT(*) >= 60 THEN 'TOP'
                WHEN COUNT(*) BETWEEN 50 AND 59 THEN 'HIGH'
                WHEN COUNT(*) BETWEEN 40 AND 49 THEN 'MODERATE'
                WHEN COUNT(*) <= 39 THEN 'LOW'
            END AS SCORE_CATEGORY
            FROM
            VENDOR_DETAILS
            GROUP BY
            VENDOR_NAME
        ) AS SC ON VD.VENDOR_NAME = SC.VENDOR_NAME
        WHERE 
            VD.PRODUCT_NAME LIKE :vendorName 
            GROUP BY 
        SC.SCORE_CATEGORY";

        $stmt = $pdo->prepare($sql);
        $likeVendorName = '%' . $vendorName . '%';
    $stmt->bindParam(':vendorName', $likeVendorName, PDO::PARAM_STR);
        $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array('items' => $result));
    echo $json;
}


function loadStateData() {
    $vendorName = !empty($vendorName) ? $vendorName : " ";
    $pdo = getConnection();
    $sql = "SELECT STATE, SUM(ANNUAL_SALES) AS total_sales FROM VENDOR_DETAILS GROUP BY STATE";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array('items' => $result));
    echo $json;
}
?>