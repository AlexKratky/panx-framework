# Pagination
Pagination is class that splits the data to segments and browse between these segments.
The basic usage:
```php
//example data
$data = array(0,1,2,3,4,5,6,7,8,9);
//enter data and how many elements are on page (3). By default 10.
$pagination = new Pagination($data, 3, Pagination::DATA_ARRAY);
$d = $pagination->getData(); // [0,1,2]
$pagination->currentPage(); // 1
$pagination->totalPage(); // 4 (floor(10/3)+1)
$pagination->previousPage(); // false; because the first page is the lowest one
$pagination->nextPage(); // 2
```
The current page is set by Route::getValue("{PAGE}"); so just use in your routes {PAGE} parameter. Or it could be done by get parameter ?page= ($_GET["page"]).

### Supported types

The pagination support for array, file or SQL data type. All you need to change is in the construct the data type (3rd parameter) to Pagination::DATA_ARRAY, Pagination::DATA_SQL or Pagination::DATA_FILE. Then, in $data (1st parameter) you enter the array, sub query (e.g. `FROM table WHERE x=10` - You do not write SELECT, LIMIT etc. because that is the work of Pagination class) or the file path, everything else is the same.

## InfinityScrolling
The InfinityScrolling is script, that will automatically load next page using AJAX when the user hits the bottom of page. Everything you need to do is calling this static method Pagination::infinityScroll() inside the DOM element, where the data will be loaded. Also, for this you need route CURRENT_URI_WHERE_IS_INFINITY_SCROLL_USED/load/{PAGE} which will serve the data.