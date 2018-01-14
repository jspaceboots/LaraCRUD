

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create{{$model}}sTable extends Migration
{
    public function up()
    {
        Schema::create('{{$table}}', function (Blueprint $table) {
            @if(config('crud.useUuids'))
                $table->uuid('id');
                $table->primary('id');
            @else
                $table->increments('id');
            @endif

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{{$table}}');
    }
}
