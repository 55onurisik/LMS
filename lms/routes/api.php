
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\TopicController;

Route::get('/classes/{classId}/units', [UnitController::class, 'getUnitsByClass']);
Route::get('/units/{unitId}/topics', [TopicController::class, 'getTopicsByUnit']);
