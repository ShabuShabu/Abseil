<p align="center">
    <img alt="" src="https://github.com/boris-glumpler/Abseil/blob/develop/abseil.png"/>
</p>

<h1 align="center">Abseil</h1>

<p align="center">
    <a href="https://github.com/ShabuShabu/Abseil/actions?query=workflow%3A%22Run+PHPUnit+tests%22">
        <img alt="PHPUnit Tests" src="https://github.com/ShabuShabu/Abseil/workflows/Run%20PHPUnit%20tests/badge.svg"/>
    </a>
    <a href="https://github.com/ShabuShabu/Abseil/blob/develop/LICENSE.md">
        <img alt="GitHub license" src="https://img.shields.io/github/license/ShabuShabu/Abseil">
    </a>
</p>

Taking some of the pain out of creating a [JSON:API](https://jsonapi.org/) in your [Laravel](https://laravel.com/) app

## ToDo

- Extract tests from original package
- Publish to Packagist
- Add a middleware to check for [valid headers](https://jsonapi.org/format/#content-negotiation-servers)
- Enjoy rock star status and live the good life

## Installation

You will eventually be able to install the package via composer (:bangbang: once it's been published to Packagist...):

```
$ composer require shabushabu/abseil
```

### Morph Map

If you don't use a [morph map](https://laravel.com/docs/7.x/eloquent-relationships#custom-polymorphic-types) yet, now's the time to get your foot in the door. Just chuck all your models in there so Abseil can do it's magic.
Keep the keys the same as your route parameters, btw. Strictly speaking Abseil only requires the `MORPH_MAP` constant, but you might as well go all in. Bet on that rope to hold your weight...

```php
namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use [...];

class AppServiceProvider extends ServiceProvider
{
    public const MORPH_MAP = [
        'category' => Category::class,
        'page'     => Page::class,
        'user'     => User::class,
    ];

    public function boot(): void
    {
        Relation::morphMap(self::MORPH_MAP);
    }
}
```

The `MORPH_MAP` constant will then be used to add uuid patterns for your route model binding (disable in config if you use auto-incrementing ids).
It'll also allow you to use the fantastic [query builder package from Spatie](https://github.com/spatie/laravel-query-builder) for your single models and not just for collections.

### Class Constants

Abseil expects certain class constants to be present on your models. We've already talked about `MORPH_MAP`, but there are also `JSON_TYPE` and `ROUTE_PARAM`. These should be set on each model.

```php
use ShabuShabu\Abseil\Model;

class Category extends Model
{
    public const JSON_TYPE = 'categories';
    public const ROUTE_PARAM = 'category';
}
```

`ShabuShabu\Abseil\Model` is there to make things easier for you, but if you use auto-incrementing ids then you will have to create your own base model using the Abseil model as a guide.

## Usage

[Eloquent Resources](https://laravel.com/docs/7.x/eloquent-resources) and the JSON:API standard have a bit of a rocky relationship.
"We're just way too different", JSON:API might say. "That upstart of a spec is just too opinionated", Laravel might retort.
They tolerate each other and get along for the most part but won't ever be best mates, they feel. 
Abseil is here to make them rethink their relationship.

### Controllers

Most Abseil controllers will look something like this. Just make sure that your controller extends the Abseil controller and you're all set.
Please note, that Abseil controllers expect [ShabuShabu Harness requests](https://github.com/ShabuShabu/Harness) for any save operation.

```php
namespace App\Http\Controllers;

use App\Http\Requests\PageRequest;
use App\Http\Resources\Page as PageResponse;
use App\Page;
use Illuminate\Http\{Request, Response};
use ShabuShabu\Abseil\Http\{Collection, Controller};

class PageController extends Controller
{
    public function index(Request $request): Collection
    {
        return $this->resourceCollection(Page::class, $request);
    }

    public function store(PageRequest $request): Response
    {
        return $this->createResource($request, Page::class);
    }

    public function show(Request $request, Page $page): PageResponse
    {
        return $this->showResource($request, $page);
    }

    public function update(PageRequest $request, Page $page): Response
    {
        return $this->updateResource($request, $page);
    }

    public function destroy(Page $page): Response
    {
        return $this->deleteResource($page);
    }

    public function restore(Page $page): Response
    {
        return $this->restoreResource($page);
    }
}
```

### Events

Abseil throws a number of events that you can hook into. Here's a full list:

- **ResourceCreated**
- **ResourceUpdated**
- **ResourceDeleted**
- **ResourceRelationshipSaved**

The names are kinda self-explanatory. The payload for each event is always the model in question.
`ResourceRelationshipSaved` is fired once for each relationship model that has been saved.

### Relationships

Abseil makes it easier to save any relationships via a JSON:API `POST` or `PUT` request.
It does this by looping through the `data.relationships` array, figuring out which relation it needs and then calling a `sync{relationship-name}` method on the model.

Here's an example request payload:

```json
{
    "data": {
        "id": "904754f0-7faa-4872-b7b8-2e2556d7a7bc",
        "type": "pages",
        "attributes": {
            "title": "Some title",
            "content": "Lorem what?"
        },
        "relationships": {
            "category": {
                "data": {
                    "type": "categories",
                    "id": "9041eabb-932a-4d47-a767-6c799873354a"
                }
            }
        }
    }
}
```

Abseil will then call the following method so you can save the category as you see fit:

```php
$page->syncCategory(collect([
    'type' => 'categories',
    'id' => '9041eabb-932a-4d47-a767-6c799873354a'
]));
```

Abseil will throw an error if that method does not exist, so it's your responsibility to make sure it's there when you allow saving relationships via ShabuShabu Harness requests.

Staying with this example, the `Page::syncCategory` method could be as easy as the following:

```php
public function syncCategory(Collection $category): void
{
    $this->category_id = $category->get('id');
    $this->save();
}
```

### Resources

Resources have always been my biggest stumbling block when trying to create a valid JSON:API. With Abseil, though, the following is possible:
Note that we're only specifying the `data.attributes` here. Anything else, like relationships, will be handled for you.

```php
namespace App\Http\Resources;

use ShabuShabu\Abseil\Http\Resource; 

class Page extends Resource
{
    public function resourceAttributes($request): array
    {
        return [
            'title'       => (string)$this->title,
            'content'     => (string)$this->content,
            'createdAt'   => $this->date($this->created_at),
            'updatedAt'   => $this->date($this->updated_at),
            'deletedAt'   => $this->date($this->deleted_at),
        ];
    }
}
```

If, for example, there is a user relationship and it was specified via the `include` query parameter, then Abseil will load that relationship automatically for you and attach it to the response.
[ShabuShabu Belay](https://github.com/ShabuShabus/Belay) is the perfect counter part for Abseil and will handle the JS side of things. Great if you want to create a client app for your API using Vue or Nuxt.

### Resource Collections

Collection resources do not need to be specifically created, although you can if you want to. Abseil will use its own collection class by default for any `index` responses.
One thing to note here is that the default Laravel pagination data is being transformed to camel-case:

```json
{
    [...]
    "meta": {
        "pagination": {
            "currentPage": 1,
            "from": 1,
            "lastPage": 1,
            "path": "https:\/\/awesome-api.com\/pages",
            "perPage": 20,
            "to": 7,
            "total": 7
        }
    },
    [...]
}
```

All models should also implement the `Queryable` interface. It only requires a single method: `modifyPagedQuery`.

```php
/**
 * Modify the query
 *
 * @param \Spatie\QueryBuilder\QueryBuilder $query
 * @param \Illuminate\Http\Request $request
 * @return \Spatie\QueryBuilder\QueryBuilder
 */
public static function modifyPagedQuery(QueryBuilder $query, Request $request): QueryBuilder;
```

Here you can then [configure the query builder](https://docs.spatie.be/laravel-query-builder/v2/introduction/), add sorts, includes, filters, etc.

### Routing

The only thing Abseil expects form your application as far as routing is concerned is that you name your single `GET` routes according to a certain convention:

```
'{JSON_TYPE}.show'
```

That way, Abseil can automatically create the links section for you.

## Testing

Abseil has been extracted from a [personal project](https://boris.travelled.today). It is fully unit tested, but the tests are still intermingled with original project tests and will make their way into this repository in due course. 

```
$ composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email boris@shabushabu.eu instead of using the issue tracker.

## :bangbang: Caveats

Abseil is still young and while it is tested, there will probs be bugs. I will try to iron them out as I find them, but until there's a v1 release, expect things to go :boom:.

## Credits

- [All Contributors](../../contributors)
- [BTT](https://boris.travelled.today), aka **Boris Travelled Today**, where Abseil was extracted from
- [Ivan Boyko](https://www.iconfinder.com/visualpharm) [[cc]](https://creativecommons.org/licenses/by/3.0/) for the abseil icon

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
