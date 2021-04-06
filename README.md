# UniversityCalendar

Simple implementation of calendar with university exams schedule payload

## Usage

Open [calendar.php script](calendar.php) to get calendar page of current month. <br/>
Or pass ***year*** and ***month*** get parameter into url for get calendar page of certain month.

Open [day.php script](day.php) for getting list of exams for current day. <br/>
[day.php](day.php) receiving ***date*** get parameter which describe certain day in unix_timestamp format.

## implementation

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

## API methods

Route all API queries to [api.php script](includes/api.php) passing ***module*** get parameter to specify type of called method.
</br>

### `get_day_schedules`
`vzlet-1.ru/UniversityCalendar/includes/api.php?module=get_day_schedules`

Getting a list of exam schedules and specialities taking these exams for current day.

### parameters
*integer:* ***date*** - unix_timestamp of current day

### example returning value
```json
{
  "schedules": [
    {
      "date": "2021-03-30 12:00:00",
      "title": "Math",
      "specialities": [
        {
          "code": "15VV2",
          "title": "Computer science and Engineering (CT)"
        }
      ]
    },
    {
      "date": "2021-03-30 14:00:00",
      "title": "Programming",
      "specialities": [
        {
          "code": "17VV3",
          "title": "Computer science and Engineering (CAD)"
        },
        {
          "code": "17VV2",
          "title": "Computer science and Engineering (CT)"
        }
      ]
    }
  ]
}
```
</br>

### `get_interval_schedules`
`vzlet-1.ru/UniversityCalendar/includes/api.php?module=get_interval_schedules`

Getting day list on which exams are scheduled, including count of scheduled exams in date range.

### parameters
*integer:* ***start_date*** - unix_timestamp describing start of date range</br>
*integer:* ***end_date*** - unix_timestamp describing end of date range

### example returning value

```json
{
  "interval_schedules": [
    {
      "count": "3",
      "timestamp": "1617137999"
    },
    {
      "count": "1",
      "timestamp": "1617397199"
    },
    {
      "count": "1",
      "timestamp": "1617420964"
    }
  ]
}
```
</br>

### `add_schedule`
`vzlet-1.ru/UniversityCalendar/includes/api.php?module=add_schedule`

Add new exam's schedule of existing subject

### parameters
*integer:* ***id_subject*** - id of existing subject in database</br>
*integer:* ***date*** - datetime on which exam are planned

### example returning value
*in case of success adding*
```json
{
  "status": true
}
```
*in case of failure adding*
```json
{
  "status": false
}
```
<hr>

Created by [Arthur](https://vk.com/id150530376)
