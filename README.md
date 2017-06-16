# ProStats v.1.9.7 Corrected

Recent PHP updates have secured variable handling.

This results in [prostats.php](source/inc/plugins/prostats.php):

> **Illegal string offset 'xxxxxxxxxxx' on line 1247, 1253, 1258, 1263, 1268**

The idea here is to understand and correct the problem.

# Understanding the problem

If we look at the lines in question we have:

```php
$active_cells = 1;
```

And

```php
$active_cells["XXXX"]=1;
```

Where `XXXX` represent the different variables from the code.

The second statement is basically assigning the value `1` to the key `XXXX`

However if we look at the `$active_cells` declaration:

```php
$active_cells = "";
```

We can see here that our variable is declared as a **string** and not an **array**

# The Fix

The way to fix this is by declaring the `$active_cells` as an **array**

Replacing:

```php
$active_cells = "";
```

By:

```php
$active_cells = array("foo" => "bar","bar" => "foo");
```
