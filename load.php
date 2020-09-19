<?php   
    function loadFromCsv(){
        //data from .csv
        $fileName="products with headers.csv";
        $fp=fopen(str_replace(" ", "\x20", $fileName),"r");
        $keys="";
        $products =[];
        $rows=[];
        while($red=fgetcsv($fp)){
            $jsonRed=[];
            if($keys){
                $i=0;
                foreach($red as $clan){
                    if(isset($clan))
                        $jsonRed[trim($keys[$i])]=addslashes($clan);
                    else 
                        $jsonRed[trim($keys[$i])]="";
                    $i++;
                }
                array_push($products,$jsonRed);       
            }
            else
                $keys=$red;
        }
        for($i=0;$i<count($keys);$i++)
            $rows[trim($keys[$i])]=[];
        
        for($i=0;$i<count($products);$i++){
            for($j=0;$j<count($keys);$j++)
            if(isset($products[$i][$keys[$j]]))
                if(!in_array($products[$i][$keys[$j]],$rows[trim($keys[$j])]))
                    if(trim($keys[$j])=='category_name'){
                        $chk=false;
                        for($k=0;$k<count($rows[trim($keys[$j])]);$k++){
                            if(isset($products[$i]['department_name']))
                                if($rows[trim($keys[$j])][$k][0]==$products[$i]['category_name'] && $rows[trim($keys[$j])][$k][1]=$products[$i]['department_name'])
                                    $chk=true;
                            else 
                                if($rows[trim($keys[$j])][$k][0]== $products[$i]['category_name'] )
                                    $chk=true;
                        }
                        if(!$chk){
                            if(isset($products[$i]['department_name']))
                                array_push($rows[trim($keys[$j])],[$products[$i][trim($keys[$j])],$products[$i]['department_name']]);
                            else
                                array_push($rows[trim($keys[$j])],[$products[$i][trim($keys[$j])]]);
                        }
                    }
                    else
                        array_push($rows[trim($keys[$j])],str_replace('"', "'", $products[$i][trim($keys[$j])]));
        }
        //empty tables
        $con=mysqli_connect("localhost", "root", "", "task");
        if(mysqli_connect_error())
        {
            echo "Connect to database failed!";
            exit();
        }
        else {
            mysqli_query($con, "DELETE FROM products");
            mysqli_query($con, "DELETE FROM categories");
            mysqli_query($con, "DELETE FROM manufacturers");
            mysqli_query($con, "DELETE FROM departments");
            echo mysqli_error($con);
            mysqli_close($con);
        }
        //import 
        $stringMan="";
        $stringKat="";
        $stringDep="";
        $stringItems="";
        $i=0;
        foreach ($rows['manufacturer_name'] as $manufacturer){
            if($i==0)
                $stringMan.="('".$manufacturer."')";
            else
                $stringMan.=",('".$manufacturer."')";
            $i++;
        }
        $i=0;
        $rows['department_name']=array_unique($rows['department_name']);
        foreach ($rows['department_name'] as $department_name){
            if($department_name!="")
                if($i==0)
                    $stringDep.="('".addslashes($department_name)."')";
                else
                    $stringDep.=",('".addslashes($department_name)."')";
            $i++;
        }
        for($i=0;$i<count($rows['category_name']);$i++)
            if($i==0)
                if(isset($rows['category_name'][$i][1]))
                    $stringKat.="('".$rows['category_name'][$i][0]."',(SELECT idDepartment FROM departments WHERE nameDepartment='".$rows['category_name'][$i][1]."'))";
                else
                    $stringKat.="('".$rows['category_name'][$i][0]."',NULL)";
            else
                if(isset($rows['category_name'][$i][1]))
                    $stringKat.=",('".$rows['category_name'][$i][0]."',(SELECT idDepartment FROM departments WHERE nameDepartment='".$rows['category_name'][$i][1]."'))";
                else 
                $stringKat.=",('".$rows['category_name'][$i][0]."',NULL)";
        
    
        $con=mysqli_connect("localhost", "root", "", "task");
        if(mysqli_connect_error())
        {
            echo "Connect to database failed!";
            exit();
        }
        else {
            $i=0;
            foreach($products as $product){ 
                //echo "<br><br>('".$product['model_number']."','".$product["upc"]."','".$product["sku"]."',".$product["regular_price"].",".$product["sale_price"].",'".addslashes(str_replace(",",",",str_replace("'", '"', $product["description"])))."','".$product["url"]."',(SELECT idCategory FROM categories WHERE nameCategory='".$product["category_name"]."'),(SELECT idManufacturer FROM manufacturers WHERE nameManufacturer='".$product["manufacturer_name"]."'))";
                if($i==0)
                    $stringItems.="('".$product['model_number']."','".$product["upc"]."','".$product["sku"]."',".$product["regular_price"].",".$product["sale_price"].",'".addslashes(str_replace(",",",",str_replace("'", '"', $product["description"])))."','".$product["url"]."',(SELECT idCategory FROM categories WHERE nameCategory='".$product["category_name"]."'),(SELECT idManufacturer FROM manufacturers WHERE nameManufacturer='".$product["manufacturer_name"]."'))";
                else
                    $stringItems.=",('".$product["model_number"]."','".$product["upc"]."','".$product["sku"]."',".$product["regular_price"].",".$product["sale_price"].",'".addslashes(str_replace(",",",",str_replace("'", '"', $product["description"])))."','".$product["url"]."',(SELECT idCategory FROM categories WHERE nameCategory='".$product["category_name"]."'),(SELECT idManufacturer FROM manufacturers WHERE nameManufacturer='".$product['manufacturer_name']."'))";       
                $i++;
            }
            $query="INSERT INTO manufacturers (nameManufacturer) VALUES ".$stringMan.";";
            mysqli_query($con,$query);
            
            $query="INSERT INTO departments (nameDepartment) VALUES ".$stringDep.";";
            mysqli_query($con,$query);
            $query="INSERT INTO categories (nameCategory, idDepartment) VALUES ".$stringKat.";";
            mysqli_query($con,$query);
            $query="INSERT INTO products (model_number, upc, sku, regular_price, sale_price, description, url, idCategory, idManufacturer) VALUES ".$stringItems.";";
            mysqli_query($con,$query);
           
            mysqli_close($con);
        }
    }
    function getProducts($category=""){
        $data=[];
        $con=mysqli_connect("localhost", "root", "", "task");
        if(mysqli_connect_error())
        {
            echo "Connect to database failed!";
            exit();
        }
        else {
            if($category!="")
                $rows=mysqli_query($con,"select * from  view_products where idCategory=".$category.";");
            else
                $rows=mysqli_query($con,"select * from  view_products");
            if(!mysqli_error($con)){
                for($i=0;$i<mysqli_num_rows($rows);$i++){
                    $row=mysqli_fetch_assoc($rows);
                    array_push($data,$row);
                }
            }
            else echo mysqli_error($con);
            mysqli_close($con);
        }
        return $data;
    }
    function getManufacturers(){
        $data=[];
        $con=mysqli_connect("localhost", "root", "", "task");
        if(mysqli_connect_error())
        {
            echo "Connect to database failed!";
            exit();
        }
        else {
            $rows=mysqli_query($con,"select * from  manufacturers");
            if(!mysqli_error($con)){
                for($i=0;$i<mysqli_num_rows($rows);$i++){
                    $row=mysqli_fetch_assoc($rows);
                    array_push($data,$row);
                }
            }
            mysqli_close($con);
        }
        return $data;
    }
    function getCategories(){
        $data=[];
        $con=mysqli_connect("localhost", "root", "", "task");
        if(mysqli_connect_error())
        {
            echo "Connect to database failed!";
            exit();
        }
        else {
            $rows=mysqli_query($con,"select * from  categories");
            if(!mysqli_error($con)){
                for($i=0;$i<mysqli_num_rows($rows);$i++){
                    $row=mysqli_fetch_assoc($rows);
                    array_push($data,$row);
                }
            }
            mysqli_close($con);
        }
        return $data;
    }
    function getDepartments(){
        $data=[];
        $con=mysqli_connect("localhost", "root", "", "task");
        if(mysqli_connect_error())
        {
            echo "Connect to database failed!";
            exit();
        }
        else {
            $rows=mysqli_query($con,"select * from departments");
            if(!mysqli_error($con)){
                for($i=0;$i<mysqli_num_rows($rows);$i++){
                    $row=mysqli_fetch_assoc($rows);
                    array_push($data,$row);
                }
            }
            mysqli_close($con);
        }
        return $data;
    }
?>