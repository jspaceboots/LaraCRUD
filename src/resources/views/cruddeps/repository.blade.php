
namespace {{ltrim(rtrim(config('crud.namespaces.repositories'), '\\'), '\\')}};

class {{$model}}Repository extends \jspaceboots\laracrud\Repositories\AbstractRepository {

}