

namespace {{ltrim(rtrim(config('crud.namespaces.transformers'), '\\'), '\\')}};

class {{$model}}Transformer extends \jspaceboots\laracrud\Transformers\AbstractTransformer {

    /*
    protected function toHTML($entity) {
        return [
            '' => $entity->
        ];
    }
    */

    /*
    protected function toJSON($entity) {
        return [
            '' => $entity->
        ];
    }
    */
}