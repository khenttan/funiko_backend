<?php

namespace Bpocallaghan\LogActivity\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LogModelActivity extends Model
{
    protected $table = 'log_model_activities';

    protected $guarded = ['id'];

    /**
     * Get the user responsible for the given activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity.
     *
     * @return mixed
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get the latest activities on the site
     * @return mixed
     */
    public static function getLatest()
    {
        return self::with('user')
            ->with('subject')
            ->orderBy('created_at', 'DESC')
            ->limit(100)
            ->get();
    }
}
