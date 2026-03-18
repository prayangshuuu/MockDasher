<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $test_attempt_id
 * @property int $question_id
 * @property string|null $answer_text
 * @property int $is_flagged
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ListeningAttempt $attempt
 * @property-read \App\Models\Question $question
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer whereAnswerText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer whereIsFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer whereTestAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAnswer whereUserId($value)
 */
	class ListeningAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $current_section
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $transfer_started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $test_set_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ListeningAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read float $band_score
 * @property-read \App\Models\TestSet|null $testSet
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereCurrentSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereTestSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereTransferStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningAttempt whereUserId($value)
 */
	class ListeningAttempt extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $section_number
 * @property string|null $instruction_text
 * @property string|null $audio_path
 * @property string|null $passage_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $test_set_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @property-read \App\Models\TestSet|null $testSet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection whereAudioPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection whereInstructionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection wherePassageText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection whereSectionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection whereTestSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ListeningSection whereUpdatedAt($value)
 */
	class ListeningSection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $questionable_type
 * @property int $questionable_id
 * @property string $question_type
 * @property string|null $question_text
 * @property string|null $correct_answer
 * @property string|null $explanation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionOption> $options
 * @property-read int|null $options_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $questionable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCorrectAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereQuestionableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Question whereUpdatedAt($value)
 */
	class Question extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $question_id
 * @property string $option_text
 * @property int $is_correct
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Question $question
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereOptionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereUpdatedAt($value)
 */
	class QuestionOption extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $test_attempt_id
 * @property int $question_id
 * @property string|null $answer_text
 * @property int $is_flagged
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ReadingAttempt $attempt
 * @property-read \App\Models\Question $question
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer whereAnswerText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer whereIsFlagged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer whereTestAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAnswer whereUserId($value)
 */
	class ReadingAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $test_set_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReadingAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read float $band_score
 * @property-read \App\Models\TestSet|null $testSet
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt whereTestSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingAttempt whereUserId($value)
 */
	class ReadingAttempt extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $passage_number
 * @property string|null $title
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $test_set_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReadingQuestionGroup> $questionGroups
 * @property-read int|null $question_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @property-read \App\Models\TestSet|null $testSet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage wherePassageNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage whereTestSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingPassage whereUpdatedAt($value)
 */
	class ReadingPassage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $reading_passage_id
 * @property string|null $group_instruction
 * @property string $question_type
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ReadingPassage $passage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup whereGroupInstruction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup whereReadingPassageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReadingQuestionGroup whereUpdatedAt($value)
 */
	class ReadingQuestionGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $test_set_id
 * @property-read \App\Models\TestSet|null $testSet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTestSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 */
	class Section extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $part
 * @property string|null $question_text
 * @property string|null $audio_path
 * @property int|null $time_limit
 * @property string|null $preparation_instructions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $test_set_id
 * @property-read \App\Models\TestSet|null $testSet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion whereAudioPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion wherePart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion wherePreparationInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion whereQuestionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion whereTestSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion whereTimeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpeakingQuestion whereUpdatedAt($value)
 */
	class SpeakingQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $book_number
 * @property int|null $year
 * @property string|null $exam_type
 * @property-read mixed $title
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TestSet> $test_sets
 * @property-read int|null $test_sets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereBookNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereExamType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Test whereYear($value)
 */
	class Test extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $test_set_id
 * @property-read float|null $listening_band
 * @property-read int|null $listening_score
 * @property-read float|null $overall_band
 * @property-read float|null $reading_band
 * @property-read int|null $reading_score
 * @property-read string|null $time_spent
 * @property-read \App\Models\ListeningAttempt|null $listeningAttempt
 * @property-read \App\Models\ReadingAttempt|null $readingAttempt
 * @property-read \App\Models\Test|null $test
 * @property-read \App\Models\TestSet|null $testSet
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WritingAnswer> $writingAnswers
 * @property-read int|null $writing_answers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt whereTestSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestAttempt whereUserId($value)
 */
	class TestAttempt extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $test_id
 * @property int $set_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ListeningSection> $listeningSections
 * @property-read int|null $listening_sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReadingPassage> $readingPassages
 * @property-read int|null $reading_passages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpeakingQuestion> $speakingQuestions
 * @property-read int|null $speaking_questions_count
 * @property-read \App\Models\Test $test
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WritingTask> $writingTasks
 * @property-read int|null $writing_tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestSet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestSet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestSet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestSet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestSet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestSet whereSetNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestSet whereTestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TestSet whereUpdatedAt($value)
 */
	class TestSet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $profile_photo_path
 * @property string|null $country
 * @property numeric|null $target_band_score
 * @property string|null $exam_type
 * @property \Illuminate\Support\Carbon|null $exam_date
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TestAttempt> $testAttempts
 * @property-read int|null $test_attempts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WritingAnswer> $writingAnswers
 * @property-read int|null $writing_answers_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereExamDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereExamType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTargetBandScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $test_attempt_id
 * @property int $writing_task_id
 * @property string|null $answer_text
 * @property int $word_count
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TestAttempt $attempt
 * @property-read \App\Models\WritingTask $task
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereAnswerText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereTestAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereWordCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingAnswer whereWritingTaskId($value)
 */
	class WritingAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $task_number
 * @property string $task_title
 * @property string|null $task_description
 * @property string|null $task_prompt
 * @property string|null $instruction_text
 * @property int $minimum_word_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $test_set_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WritingAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WritingTaskImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\TestSet|null $testSet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereInstructionText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereMinimumWordCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereTaskDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereTaskNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereTaskPrompt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereTaskTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereTestSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTask whereUpdatedAt($value)
 */
	class WritingTask extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $writing_task_id
 * @property string $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\WritingTask $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTaskImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTaskImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTaskImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTaskImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTaskImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTaskImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTaskImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WritingTaskImage whereWritingTaskId($value)
 */
	class WritingTaskImage extends \Eloquent {}
}

