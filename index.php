<?php
    require_once("load.php");
    loadFromCsv();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div id='menu'><button onclick="menu(true)">Products</button> <button onclick="menu(false)">Categories</button></div>
    <div id="products">
        <button onclick="showAllProducts()">Show all products</button> <br>
        <select name="" id="selectCategory">
            <option value='-1'>--choose a category--</option>
            <?php
                $categories=getCategories();
                $departments=getDepartments();
                $manufacturers=getManufacturers();
                for($i=0;$i<count($categories);$i++){
                    echo '<option value="'.$categories[$i]['idCategory'].'">'.$categories[$i]['nameCategory'].'</option>';
                }
            ?>
        </select><button onclick='showProductsByCategory()'>Show products in the selected category</button><br>
        <select id="selectProducts" size="20" style="width:95%; overflow:scroll" onchange='selectProduct(this.value)'> 

        </select><br>
        Model number: <input type="text" id="modelNumber" disabled><br>
        Upc: <input type="text" id="upc"><br>
        Sku: <input type="text" id="sku"><br>
        Regular price: <input type="number" step="0.01" min='1' id="regularPrice"><br>
        Sale price: <input type="number" step="0.01" min='1' id="salePrice"><br>
        Description: <br>
        <textarea name="" id="description" cols="30" rows="10"></textarea><br>
        Category: <select name="" id="productCategory">
            <option value="-1">--choose a category--</option>
            <?php
                for($i=0;$i<count($categories);$i++)
                    echo '<option value="'.$categories[$i]['idCategory'].'">'.$categories[$i]['nameCategory'].'</option>';
            ?>
        </select><br>
        Manufacturer: <select name="" id="productManufacturer">
            <option value="-1">--choose a manufacturer--</option>
            <?php
                for($i=0;$i<count($manufacturers);$i++)
                    echo '<option value="'.$manufacturers[$i]['idManufacturer'].'">'.$manufacturers[$i]['nameManufacturer'].'</option>';
            ?>
        </select><br>
        <button onclick="changeSelectedProduct()">CHANGE SELECTED PRODUCT</button>
        <button onclick="deleteSelectedProduct()">DELETE SELECTED PRODUCT</button>
    </div>
    <div id="categories" style="display:none">
        <button onclick='showCategories()'>Show all categories >></button>
        <select name="" id="selectCategories" onchange="selectCategory(this.value)">
        </select><br><br>
        Category: <input type="text" id='category'><br><br>

        Department: <select name="" id="selectDepartment">
            <option value='-1'>--choose a department--</option>
            <?php
                for($i=0;$i<count($departments);$i++)
                    echo '<option value="'.$departments[$i]['idDepartment'].'">'.$departments[$i]['nameDepartment'].'</option>';
            ?>
        </select><br><br>
        <button onclick="changeSelectedCategory()">CHANGE SELECTED CATEGORY</button>
        <button onclick="deleteSelectedCategory()">DELETE SELECTED CATEGORY</button>
    </div>
    <script>
        let categories=[];
        let departments= <?php echo json_encode($departments);?>;
        let products=[];
        function menu(par){
            if(!par){
                document.getElementById("products").style.display="none";
                document.getElementById("categories").style.display="block";
            }
            else{
                document.getElementById("categories").style.display="none";
                document.getElementById("products").style.display="block";
            }
        }
        //categories
        function showCategories(){
            $.post("task.php?task=showCategories",function(e){
                str="<option value='-1'>--choose a category--</option>";
                categories=JSON.parse(e);
                for(i=0;i<categories.length;i++)
                    str+="<option value='"+categories[i]['idCategory']+"'>"+categories[i]['nameCategory']+"</option>";
                document.getElementById("selectCategories").innerHTML=str;    
            })
        }
        function selectCategory(obj){
            if(obj!="-1"){
                for(i=0;i<categories.length;i++)
                    if(categories[i]['idCategory']==obj){
                        document.getElementById("category").value=categories[i]['nameCategory'];
                        document.getElementById("selectDepartment").value=categories[i]['idDepartment'];
                    }   
            }
            else{
                document.getElementById("category").value="";
                document.getElementById("selectDepartment").value="-1";
            }
        }
        function changeSelectedCategory(){
            if(document.getElementById("selectCategories").value!="-1" && document.getElementById("selectCategories").value!=""){
                if(document.getElementById("category").value!="" && document.getElementById("selectDepartment").value!="-1"){
                    $.post("task.php?task=changeCategory",{
                        idCategory:document.getElementById("selectCategories").value,
                        nameCategory:document.getElementById("category").value,
                        idDepartment: document.getElementById("selectDepartment").value
                    },function(e){
                        alert(e);
                        if(e=='Successfully changed'){
                            //other select categories
                            for(i=0;i<categories.length;i++)
                                if(categories[i]['idCategory']==document.getElementById("selectCategories").value){
                                    categories[i]['nameCategory']=document.getElementById("category").value;
                                    categories[i]['idDepartment']=document.getElementById("selectDepartment").value;
                                }
                            writeCategories();
                        }
                    })
                }
                else 
                    alert("Enter all data"); 
            }
            else 
                alert("Choose a category");
        }
        function deleteSelectedCategory(){
            if(document.getElementById("selectCategories").value!="-1" && document.getElementById("selectCategories").value!=""){
                $.post("task.php?task=deleteCategory",{idCategory:document.getElementById("selectCategories").value}, function(e){
                    alert(e);
                    if(e=="Successfully deleted"){
                        for(i=0;i<categories.length;i++)
                            if(categories[i]['idCategory']==document.getElementById("selectCategories").value)
                                categories.splice(i,1);
                        writeCategories();
                    }
                })
            }
            else 
                alert("Select a category!");
        }
        function writeCategories(){
            str="<option value='-1'>--choose a category--</option>";
            for(i=0;i<categories.length;i++)
                    str+="<option value='"+categories[i]['idCategory']+"'>"+categories[i]['nameCategory']+"</option>";
            document.getElementById("selectCategories").innerHTML=str;    
            document.getElementById("selectCategory").innerHTML=str;    
            document.getElementById("productCategory").innerHTML=str;    
            document.getElementById("selectProducts").innerHTML="";  
            document.getElementById("category").value="";
            document.getElementById("selectDepartment").value="-1";  
        }
        //products
        function showAllProducts(){
            $.post("task.php?task=showAllProducts",function(e){
                products=JSON.parse(e);
                listItems(JSON.parse(e));
            })
        }
        function showProductsByCategory(){
            if(document.getElementById("selectCategory").value!="-1"){
                $.post("task.php?task=showProductsByCategory",{category:document.getElementById("selectCategory").value}, function(e){
                    listItems(JSON.parse(e));
                })
            }
        }
        function listItems(items){
            str="";
            for(i=0;i<items.length;i++)
                str+="<option value='"+items[i]['idProduct']+"'>"+JSON.stringify(items[i])+"</option>";
            
            document.getElementById("selectProducts").innerHTML=str;
            clearInputs();
        }
        function clearInputs(){
            document.getElementById("modelNumber").value="";
            document.getElementById("upc").value="";
            document.getElementById("sku").value="";
            document.getElementById("regularPrice").value="";
            document.getElementById("salePrice").value="";
            document.getElementById("description").value="";
            document.getElementById("productManufacturer").value="-1";
            document.getElementById("productCategory").value="-1";
        }
        function selectProduct(obj){
            for(i=0;i<products.length;i++)
                if(products[i]['idProduct']==obj){
                    document.getElementById("modelNumber").value=products[i]['model_number']
                    document.getElementById("upc").value=products[i]['upc']
                    document.getElementById("sku").value=products[i]['sku']
                    document.getElementById("regularPrice").value=products[i]['regular_price']
                    document.getElementById("salePrice").value=products[i]['sale_price']
                    document.getElementById("description").value=products[i]['description']
                    document.getElementById("productManufacturer").value=products[i]['idManufacturer']
                    document.getElementById("productCategory").value=products[i]['idCategory']
                }
        }
        function deleteSelectedProduct(){
            if(document.getElementById("selectProducts").value!=""){
                $.post("task.php?task=deleteProduct",{idProduct:document.getElementById("selectProducts").value}, function(e){
                    alert(e);
                    clearInputs();
                    document.getElementById("selectProducts").innerHTML="";
                })
            }
            else 
                alert("Select a product!");
        }
        function changeSelectedProduct(){
            if(document.getElementById("selectProducts").value!=""){
                if(document.getElementById("modelNumber").value!="" && document.getElementById("upc").value!="" && document.getElementById("sku").value!="" && document.getElementById("regularPrice").value!="" && document.getElementById("salePrice").value!="" && document.getElementById("description").value!="" && document.getElementById("productManufacturer").value!="-1" && document.getElementById("productCategory").value!="-1"){
                    $.post("task.php?task=changeProduct",{
                        idProduct:document.getElementById("selectProducts").value,
                        model_number:document.getElementById("modelNumber").value,
                        upc:document.getElementById("upc").value,
                        sku:document.getElementById("sku").value,
                        regular_price:document.getElementById("regularPrice").value,
                        sale_price:document.getElementById("salePrice").value,
                        description:document.getElementById("description").value,
                        idManufacturer:document.getElementById("productManufacturer").value,
                        idCategory:document.getElementById("productCategory").value
                    }, function(e){
                        alert(e);
                        clearInputs();
                        document.getElementById("selectProducts").innerHTML="";
                    })
                }
                else
                    alert("You haven't entered all data");
            }
            else 
                alert("Select a product!");
        }
    </script>
</body>
</html>