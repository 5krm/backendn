<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('job_title')->nullable()->after('phone');
            $table->string('job_title_en')->nullable()->after('job_title');
            $table->text('bio')->nullable()->after('job_title_en');
            $table->text('bio_en')->nullable()->after('bio');
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('platform');
            $table->string('url');
            $table->timestamps();

            $table->unique(['user_id', 'platform']);
        });

        $socialColumns = ['website', 'linkedin', 'twitter', 'facebook', 'instagram'];

        DB::table('tutors')
            ->orderBy('id')
            ->chunkById(100, function ($tutors) use ($socialColumns) {
                foreach ($tutors as $tutor) {
                    $userUpdates = [];

                    if (! empty($tutor->bio)) {
                        $userUpdates['bio'] = $tutor->bio;
                    }
                    if (! empty($tutor->bio_en)) {
                        $userUpdates['bio_en'] = $tutor->bio_en;
                    }

                    $user = DB::table('users')->where('id', $tutor->user_id)->first();
                    if ($user && empty($user->phone) && ! empty($tutor->phone)) {
                        $userUpdates['phone'] = $tutor->phone;
                    }

                    if ($userUpdates !== []) {
                        DB::table('users')->where('id', $tutor->user_id)->update($userUpdates);
                    }

                    foreach ($socialColumns as $column) {
                        $url = $tutor->{$column} ?? null;
                        if (empty($url)) {
                            continue;
                        }

                        $platform = $column === 'twitter' ? 'x' : $column;

                        DB::table('social_links')->updateOrInsert(
                            [
                                'user_id' => $tutor->user_id,
                                'platform' => $platform,
                            ],
                            [
                                'url' => $url,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                }
            });

        Schema::table('tutors', function (Blueprint $table) use ($socialColumns) {
            $columns = array_merge(['phone', 'bio', 'bio_en'], $socialColumns);
            $existing = array_values(array_filter(
                $columns,
                fn (string $column) => Schema::hasColumn('tutors', $column)
            ));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tutors', function (Blueprint $table) {
            if (! Schema::hasColumn('tutors', 'bio')) {
                $table->text('bio')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('tutors', 'bio_en')) {
                $table->text('bio_en')->nullable()->after('bio');
            }
            if (! Schema::hasColumn('tutors', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (! Schema::hasColumn('tutors', 'website')) {
                $table->string('website')->nullable();
            }
            if (! Schema::hasColumn('tutors', 'linkedin')) {
                $table->string('linkedin')->nullable();
            }
            if (! Schema::hasColumn('tutors', 'twitter')) {
                $table->string('twitter')->nullable();
            }
            if (! Schema::hasColumn('tutors', 'facebook')) {
                $table->string('facebook')->nullable();
            }
            if (! Schema::hasColumn('tutors', 'instagram')) {
                $table->string('instagram')->nullable();
            }
        });

        $platformToColumn = [
            'website' => 'website',
            'linkedin' => 'linkedin',
            'x' => 'twitter',
            'facebook' => 'facebook',
            'instagram' => 'instagram',
        ];

        $users = DB::table('users')->whereNotNull('bio')
            ->orWhereNotNull('bio_en')
            ->orWhereNotNull('phone')
            ->get(['id', 'bio', 'bio_en', 'phone']);

        foreach ($users as $user) {
            DB::table('tutors')->where('user_id', $user->id)->update([
                'bio' => $user->bio,
                'bio_en' => $user->bio_en,
                'phone' => $user->phone,
            ]);
        }

        DB::table('social_links')->orderBy('id')->chunkById(100, function ($links) use ($platformToColumn) {
            foreach ($links as $link) {
                $column = $platformToColumn[$link->platform] ?? null;
                if (! $column) {
                    continue;
                }

                DB::table('tutors')->where('user_id', $link->user_id)->update([
                    $column => $link->url,
                ]);
            }
        });

        Schema::dropIfExists('social_links');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['job_title', 'job_title_en', 'bio', 'bio_en']);
        });
    }
};
