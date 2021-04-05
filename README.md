# UniversityCalendar

Simple implementation of calendar with university exams schedule payload

## Usage

Open [calendar.php script](calendar.php) to get calendar page of current month. <br/>
Or pass ***year*** and ***month*** get parameter into url for get calendar page of certain month.


Open [day.php script](day.php) for getting list of exams for current day. <br/>
[day.php](day.php) receiving ***date*** get parameter which describe certain day in unix_timestamp format.

### implementation

[Calendar class](includes/Calendar.php) implements necessary calendar's page logic.

Skip month and year parameter to generate calendar page of current date.

```php
// Generate calendar of current date
$calendar = new Calendar();
```

Or pass month and year to generate certain month's calendar page.

```php
// Create a calendar of March 2021
$calendar = new Calendar(3, 2021);
```

For returning calendar page use [get_calendar_page](includes/Calendar.php#L88-L117) method which return calendar page's day list.

```php
$calendar_days = $calendar->get_calendar_page();
```
