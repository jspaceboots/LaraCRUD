

namespace {{ltrim(rtrim(config('crud.namespaces.models'), '\\'), '\\')}};

use Illuminate\Database\Eloquent\Model;
@if(config('crud.useUuids'))
    use jspaceboots\LaraCRUD\Traits\UuidTrait;
@endif

class {{$model}} extends Model
{
    @if(config('crud.useUuids'))
        use UuidTrait;
        public $incrementing = false;
        protected $keyType = 'string';
    @endif

    protected $table = '{{$table}}';

    const validators = [];
    const relationCrud = [];
}
