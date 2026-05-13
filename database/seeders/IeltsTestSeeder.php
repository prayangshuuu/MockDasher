<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\ReadingPassage;
use App\Models\ReadingQuestionGroup;
use App\Models\Test;
use App\Models\TestSet;
use Illuminate\Database\Seeder;

class IeltsTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create the Test
        $test = Test::updateOrCreate(
            ['book_number' => 20, 'year' => 2025, 'exam_type' => 'Academic'],
            ['status' => 'published']
        );

        // Create TestSet (Test 1)
        $testSet = TestSet::updateOrCreate(
            ['test_id' => $test->id, 'set_number' => 1]
        );

        // ─── PASSAGE 1 ───
        $p1 = ReadingPassage::updateOrCreate(
            ['test_set_id' => $testSet->id, 'passage_number' => 1],
            [
                'title' => 'The kākāpō',
                'content' => $this->passage1Content(),
            ]
        );

        // ─── PASSAGE 2 ───
        $p2 = ReadingPassage::updateOrCreate(
            ['test_set_id' => $testSet->id, 'passage_number' => 2],
            [
                'title' => 'Return of the elm: reintroducing the beloved tree to Britain',
                'content' => $this->passage2Content(),
            ]
        );

        // ─── PASSAGE 3 ───
        $p3 = ReadingPassage::updateOrCreate(
            ['test_set_id' => $testSet->id, 'passage_number' => 3],
            [
                'title' => 'Stress and social media',
                'content' => $this->passage3Content(),
            ]
        );

        // ── Seed Question Groups & Questions ──

        // P1: Questions 1-6 (TFNG)
        $g1 = $this->createGroup($p1, 'true_false_not_given', 'Questions 1–6: Do the following statements agree with the information given in the passage? Write TRUE, FALSE, or NOT GIVEN.', 1);
        $this->seedQuestions($g1, 'true_false_not_given', [
            ['There are other parrots that share the kakapo\'s inability to fly.', 'FALSE'],
            ['Adult kakapo produce chicks every year.', 'FALSE'],
            ['Adult male kakapo bring food back to nesting females.', 'FALSE'],
            ['The Polynesian rat was a greater threat to the kakapo than Polynesian settlers.', 'NOT GIVEN'],
            ['Kakapo were transferred from Rakiura Island to other locations because they were at risk from feral cats.', 'TRUE'],
            ['One Recovery Plan initiative that helped increase the kakapo population size was caring for struggling young birds.', 'TRUE'],
        ]);

        // P1: Questions 7-13 (Fill in the blanks)
        $g2 = $this->createGroup($p1, 'short_answer', 'Questions 7–13: Complete the notes below. Choose ONE WORD AND/OR A NUMBER from the passage for each answer.', 2);
        $this->seedQuestions($g2, 'short_answer', [
            ['New Zealand\'s kakapo – diet consists of fern fronds, various parts of a tree and ______', 'bulbs'],
            ['Nests are created in ______ where eggs are laid', 'soil'],
            ['Arrival of Polynesian settlers – the ______ of the kakapo were used to make clothes', 'feathers'],
            ['Arrival of European colonisers – ______ were an animal which they introduced that ate the kakapo\'s food sources', 'deer'],
            ['In ______, cats were eradicated from Little Barrier Island', '1980'],
            ['A higher amount of ______ was provided for the Recovery Plan', 'funding'],
            ['The Recovery Plan aims to ensure ______ continue to be fully engaged in preservation', 'stakeholders'],
        ]);

        // P2: Questions 14-18 (MCQ matching paragraphs A-G)
        $g3 = $this->createGroup($p2, 'multiple_choice', 'Questions 14–18: The passage has seven paragraphs, A–G. Which paragraph contains the following information?', 3);
        $this->seedMCQLetters($g3, [
            ['A reference to the research problems that arise from there being only a few surviving large elms', 'C'],
            ['Details of a difference of opinion about the value of reintroducing elms to Britain', 'G'],
            ['A reference to how Dutch elm disease was brought into Britain', 'B'],
            ['A description of the conditions that have enabled a location in Britain to escape Dutch elm disease', 'E'],
            ['A reference to the stage at which young elms become vulnerable to Dutch elm disease', 'C'],
        ]);

        // P2: Questions 19-23 (MCQ matching people A-C)
        $g4 = $this->createGroup($p2, 'multiple_choice', 'Questions 19–23: Look at the following statements and the list of people below. Match each statement with the correct person, A, B or C. A. Matt Elliot  B. Karen Russell  C. Peter Bourne', 4);
        $this->seedMCQLetters($g4, [
            ['If a tree gets infected with Dutch elm disease, the damage rapidly becomes visible.', 'B'],
            ['It may be better to wait and see if the mature elms that have survived continue to flourish.', 'A'],
            ['There must be an explanation for the survival of some mature elms.', 'B'],
            ['We need to be aware that insects carrying Dutch elm disease are not very far away.', 'C'],
            ['You understand the effect Dutch elm disease has had when you see evidence of how prominent the tree once was.', 'A'],
        ]);

        // P2: Questions 24-26 (Fill in the blanks)
        $g5 = $this->createGroup($p2, 'short_answer', 'Questions 24–26: Complete the notes below. Choose ONE WORD ONLY from the passage for each answer.', 5);
        $this->seedQuestions($g5, 'short_answer', [
            ['For hundreds of years, the only tree that was more popular in Britain than elm was ______', 'oak'],
            ['In the 18th century, it was grown to provide wood for boxes and ______', 'flooring'],
            ['Due to its strength, elm was often used for mining equipment and the Cutty Sark\'s ______ was also constructed from elm', 'keel'],
        ]);

        // P3: Questions 27-30 (MCQ single answer A-D)
        $g6 = $this->createGroup($p3, 'multiple_choice', 'Questions 27–30: Choose the correct letter, A, B, C or D.', 6);
        $this->seedMCQWithOptions($g6, [
            [
                'In the first paragraph, the writer introduces the topic of the text by',
                ['defining some commonly used terms.', 'questioning a widely held assumption.', 'mentioning a challenge faced by everyone.', 'specifying a situation which makes us most anxious.'],
                'C',
            ],
            [
                'What point does the writer make about firefighters in the second paragraph?',
                ['The regular changes of stress levels in their working lives make them ideal study subjects.', 'The strategies they use to handle stress are of particular interest to researchers.', 'The stressful nature of their job is typical of many public service professions.', 'Their personalities make them especially well-suited to working under stress.'],
                'A',
            ],
            [
                'What is the writer doing in the fourth paragraph?',
                ['explaining their findings', 'justifying their approach', 'setting out their objectives', 'describing their methodology'],
                'D',
            ],
            [
                'In the seventh paragraph, the writer describes a mechanism in the brain which',
                ['enables people to respond more quickly to stressful situations.', 'results in increased ability to control our levels of anxiety.', 'produces heightened sensitivity to indications of external threats.', 'is activated when there is a need to communicate a sense of danger.'],
                'C',
            ],
        ]);

        // P3: Questions 31-35 (MCQ single answer A-G)
        $g7 = $this->createGroup($p3, 'multiple_choice', 'Questions 31–35: Complete each sentence with the correct ending, A–G. A. made them feel optimistic. B. took relatively little notice of bad news. C. responded to negative and positive information in the same way. D. were feeling under stress. E. put them in a stressful situation. F. behaved in a similar manner, regardless of the circumstances. G. thought it more likely that they would experience something bad.', 7);
        $this->seedMCQLetters($g7, [
            ['At times when they were relaxed, the firefighters usually', 'B'],
            ['The researchers noted that when the firefighters were stressed, they', 'G'],
            ['When the firefighters were told good news, they always', 'F'],
            ['The students\' cortisol levels and heart rates were affected when the researchers', 'E'],
            ['In both experiments, negative information was processed better when the subjects', 'D'],
        ]);

        // P3: Questions 36-40 (YNNG)
        $g8 = $this->createGroup($p3, 'yes_no_not_given', 'Questions 36–40: Do the following statements agree with the claims of the writer? Write YES, NO, or NOT GIVEN.', 8);
        $this->seedQuestions($g8, 'yes_no_not_given', [
            ['The tone of the content we post on social media tends to reflect the nature of the posts in our feeds.', 'YES'],
            ['Phones have a greater impact on our stress levels than other electronic media devices.', 'NOT GIVEN'],
            ['The more we read about a stressful public event on social media, the less able we are to take the information in.', 'NO'],
            ['Stress created by social media posts can lead us to take unnecessary precautions.', 'YES'],
            ['Our tendency to be affected by other people\'s moods can be used in a positive way.', 'YES'],
        ]);

        // ── Writing Tasks ──
        \App\Models\WritingTask::updateOrCreate(
            ['test_set_id' => $testSet->id, 'task_number' => 1],
            [
                'task_title' => 'Writing Task 1',
                'task_description' => 'You should spend about 20 minutes on this task.',
                'task_prompt' => 'The first table below shows changes in the total population of New York City from 1800 to 2000. The second and third tables show changes in the population of the five districts of the city (Manhattan, Brooklyn, Bronx, Queens, Staten Island) over the same period.' . "\n\n" . 'Summarise the information by selecting and reporting the main features, and make comparisons where relevant.',
                'instruction_text' => 'Write at least 150 words.',
                'minimum_word_count' => 150,
            ]
        );

        \App\Models\WritingTask::updateOrCreate(
            ['test_set_id' => $testSet->id, 'task_number' => 2],
            [
                'task_title' => 'Writing Task 2',
                'task_description' => 'You should spend about 40 minutes on this task.',
                'task_prompt' => 'Write about the following topic:' . "\n\n" . 'Access to clean water is a basic human right. Therefore every home should have a water supply that is provided free of charge.' . "\n\n" . 'Do you agree or disagree?' . "\n\n" . 'Give reasons for your answer and include any relevant examples from your own knowledge or experience.',
                'instruction_text' => 'Write at least 250 words.',
                'minimum_word_count' => 250,
            ]
        );

        // ── Speaking Questions ──
        $speakingData = [
            // Part 1: Introduction & Interview (45s per question)
            ['part' => 1, 'question_text' => 'How much walking do you do in your daily life?', 'time_limit' => 45, 'preparation_instructions' => null],
            ['part' => 1, 'question_text' => 'Did you walk more when you were at school than now?', 'time_limit' => 45, 'preparation_instructions' => null],
            ['part' => 1, 'question_text' => 'What places are there to go for a walk near where you live?', 'time_limit' => 45, 'preparation_instructions' => null],
            ['part' => 1, 'question_text' => 'Would you ever like to go on a walking holiday?', 'time_limit' => 45, 'preparation_instructions' => null],
            // Part 2: Long Turn (2:15 = 135s total)
            ['part' => 2, 'question_text' => "Describe a play or a film you have seen that you would like to see again with friends.\n\nYou should say:\n• what play or film you'd like to go to see again\n• who you would go with\n• what other people have said about this play or film\n• and explain why you would like to see this play or film again with friends.", 'time_limit' => 135, 'preparation_instructions' => 'You will have to talk about the topic for one to two minutes. You have one minute to think about what you are going to say. You can make some notes to help you if you wish.'],
            // Part 3: Discussion (90s per question)
            ['part' => 3, 'question_text' => 'What are the most popular kinds of plays or shows at theatres in your country?', 'time_limit' => 90, 'preparation_instructions' => null],
            ['part' => 3, 'question_text' => 'How easy is it to get tickets to the theatre?', 'time_limit' => 90, 'preparation_instructions' => null],
            ['part' => 3, 'question_text' => 'Do you think theatres need to do more to attract younger audiences?', 'time_limit' => 90, 'preparation_instructions' => null],
            ['part' => 3, 'question_text' => 'What do you think attracts people to working as an actor?', 'time_limit' => 90, 'preparation_instructions' => null],
            ['part' => 3, 'question_text' => 'What are some of the qualities that a person needs to have if they want to become an actor?', 'time_limit' => 90, 'preparation_instructions' => null],
            ['part' => 3, 'question_text' => 'Can you think of any disadvantages of working as an actor?', 'time_limit' => 90, 'preparation_instructions' => null],
        ];

        foreach ($speakingData as $sq) {
            \App\Models\SpeakingQuestion::updateOrCreate(
                ['test_set_id' => $testSet->id, 'part' => $sq['part'], 'question_text' => $sq['question_text']],
                ['time_limit' => $sq['time_limit'], 'preparation_instructions' => $sq['preparation_instructions']]
            );
        }

        // ── Listening Sections & Questions ──
        $sec1 = \App\Models\ListeningSection::updateOrCreate(
            ['test_set_id' => $testSet->id, 'section_number' => 1],
            ['instruction_text' => 'Complete the table below. Write ONE WORD AND/OR A NUMBER for each answer.', 'passage_text' => null]
        );
        $sec2 = \App\Models\ListeningSection::updateOrCreate(
            ['test_set_id' => $testSet->id, 'section_number' => 2],
            ['instruction_text' => 'Choose the correct letter, A, B or C.', 'passage_text' => null]
        );
        $sec3 = \App\Models\ListeningSection::updateOrCreate(
            ['test_set_id' => $testSet->id, 'section_number' => 3],
            ['instruction_text' => 'Choose TWO letters, A-E.', 'passage_text' => null]
        );
        $sec4 = \App\Models\ListeningSection::updateOrCreate(
            ['test_set_id' => $testSet->id, 'section_number' => 4],
            ['instruction_text' => 'Complete the notes below. Write ONE WORD ONLY for each answer.', 'passage_text' => null]
        );

        // Section 1: Fill-in-blank Q1-10
        $s1Questions = [
            ['Good for people who are especially keen on ______', 'fish'],
            ['The ______ is a good place for a drink', 'roof'],
            ['______ food, good for sharing', 'Spanish'],
            ['A limited selection of ______ food on the menu', 'vegetarian'],
            ['The ______', 'Audley'],
            ['At the top of a ______', 'hotel'],
            ['All the ______ are very good', 'reviews'],
            ['Only uses ______ ingredients', 'local'],
            ['Set lunch costs £______ per person', 'thirty|30'],
            ['Portions probably of ______ size', 'average'],
        ];
        foreach ($s1Questions as $sq) {
            Question::updateOrCreate(
                ['questionable_type' => \App\Models\ListeningSection::class, 'questionable_id' => $sec1->id, 'question_text' => $sq[0]],
                ['question_type' => 'short_answer', 'correct_answer' => $sq[1]]
            );
        }

        // Section 2: MCQ Q11-16
        $s2McqData = [
            ['Heather says pottery differs from other art forms because', 'A', [
                'A' => 'it lasts longer in the ground.',
                'B' => 'it is practised by more people.',
                'C' => 'it can be repaired more easily.',
            ]],
            ['Archaeologists sometimes identify the use of ancient pottery from', 'B', [
                'A' => 'the clay it was made with.',
                'B' => 'the marks that are on it.',
                'C' => 'the basic shape of it.',
            ]],
            ['Some people join Heather\'s pottery class because they want to', 'C', [
                'A' => 'create an item that looks very old.',
                'B' => 'find something that they are good at.',
                'C' => 'make something that will outlive them.',
            ]],
            ['What does Heather value most about being a potter?', 'A', [
                'A' => 'its calming effect',
                'B' => 'its messy nature',
                'C' => 'its physical benefits',
            ]],
            ['Most of the visitors to Edelman Pottery', 'B', [
                'A' => 'bring friends to join courses.',
                'B' => 'have never made a pot before.',
                'C' => 'try to learn techniques too quickly.',
            ]],
            ['Heather reminds her visitors that they should', 'C', [
                'A' => 'put on their aprons.',
                'B' => 'change their clothes.',
                'C' => 'take off their jewellery.',
            ]],
        ];
        foreach ($s2McqData as $mcq) {
            $q = Question::updateOrCreate(
                ['questionable_type' => \App\Models\ListeningSection::class, 'questionable_id' => $sec2->id, 'question_text' => $mcq[0]],
                ['question_type' => 'multiple_choice', 'correct_answer' => $mcq[1]]
            );
            foreach ($mcq[2] as $letter => $text) {
                \App\Models\QuestionOption::updateOrCreate(
                    ['question_id' => $q->id, 'option_text' => $text],
                    ['is_correct' => ($letter === $mcq[1])]
                );
            }
        }

        // Section 2: Choose TWO Q17-20
        $s2ChooseTwo = [
            ['Which TWO things does Heather explain about kilns?', 'A', [
                'A' => 'what their function is', 'B' => 'when they were invented', 'C' => 'ways of keeping them safe',
                'D' => 'where to put one in your home', 'E' => 'what some people use instead of one',
            ]],
            ['Which TWO things does Heather explain about kilns? (second answer)', 'E', [
                'A' => 'what their function is', 'B' => 'when they were invented', 'C' => 'ways of keeping them safe',
                'D' => 'where to put one in your home', 'E' => 'what some people use instead of one',
            ]],
            ['Which TWO points does Heather make about a potter\'s tools?', 'C', [
                'A' => 'Some are hard to hold.', 'B' => 'Some are worth buying.', 'C' => 'Some are essential items.',
                'D' => 'Some have memorable names.', 'E' => 'Some are available for use by participants.',
            ]],
            ['Which TWO points does Heather make about a potter\'s tools? (second answer)', 'E', [
                'A' => 'Some are hard to hold.', 'B' => 'Some are worth buying.', 'C' => 'Some are essential items.',
                'D' => 'Some have memorable names.', 'E' => 'Some are available for use by participants.',
            ]],
        ];
        foreach ($s2ChooseTwo as $ct) {
            $q = Question::updateOrCreate(
                ['questionable_type' => \App\Models\ListeningSection::class, 'questionable_id' => $sec2->id, 'question_text' => $ct[0]],
                ['question_type' => 'multiple_choice', 'correct_answer' => $ct[1]]
            );
            foreach ($ct[2] as $letter => $text) {
                \App\Models\QuestionOption::updateOrCreate(
                    ['question_id' => $q->id, 'option_text' => $text],
                    ['is_correct' => ($letter === $ct[1])]
                );
            }
        }

        // Section 3: Choose TWO Q21-26
        $s3ChooseTwo = [
            ['Which TWO things do the students both believe are responsible for the increase in loneliness?', 'C', [
                'A' => 'social media', 'B' => 'smaller nuclear families', 'C' => 'urban design',
                'D' => 'longer lifespans', 'E' => 'a mobile workforce',
            ]],
            ['Which TWO things do the students both believe are responsible for the increase in loneliness? (second answer)', 'E', [
                'A' => 'social media', 'B' => 'smaller nuclear families', 'C' => 'urban design',
                'D' => 'longer lifespans', 'E' => 'a mobile workforce',
            ]],
            ['Which TWO health risks associated with loneliness do the students agree are based on solid evidence?', 'A', [
                'A' => 'a weakened immune system', 'B' => 'dementia', 'C' => 'cancer',
                'D' => 'obesity', 'E' => 'cardiovascular disease',
            ]],
            ['Which TWO health risks associated with loneliness do the students agree are based on solid evidence? (second answer)', 'C', [
                'A' => 'a weakened immune system', 'B' => 'dementia', 'C' => 'cancer',
                'D' => 'obesity', 'E' => 'cardiovascular disease',
            ]],
            ['Which TWO opinions do both the students express about the evolutionary theory of loneliness?', 'A', [
                'A' => 'It has little practical relevance.', 'B' => 'It needs further investigation.', 'C' => 'It is misleading.',
                'D' => 'It should be more widely accepted.', 'E' => 'It is difficult to understand.',
            ]],
            ['Which TWO opinions do both the students express about the evolutionary theory of loneliness? (second answer)', 'B', [
                'A' => 'It has little practical relevance.', 'B' => 'It needs further investigation.', 'C' => 'It is misleading.',
                'D' => 'It should be more widely accepted.', 'E' => 'It is difficult to understand.',
            ]],
        ];
        foreach ($s3ChooseTwo as $ct) {
            $q = Question::updateOrCreate(
                ['questionable_type' => \App\Models\ListeningSection::class, 'questionable_id' => $sec3->id, 'question_text' => $ct[0]],
                ['question_type' => 'multiple_choice', 'correct_answer' => $ct[1]]
            );
            foreach ($ct[2] as $letter => $text) {
                \App\Models\QuestionOption::updateOrCreate(
                    ['question_id' => $q->id, 'option_text' => $text],
                    ['is_correct' => ($letter === $ct[1])]
                );
            }
        }

        // Section 3: MCQ Q27-30
        $s3McqData = [
            ['When comparing loneliness to depression, the students', 'A', [
                'A' => 'doubt that there will ever be a medical cure for loneliness.',
                'B' => 'claim that the link between loneliness and mental health is overstated.',
                'C' => 'express frustration that loneliness is not taken more seriously.',
            ]],
            ['Why do the students decide to start their presentation with an example from their own experience?', 'B', [
                'A' => 'to explain how difficult loneliness can be',
                'B' => 'to highlight a situation that most students will recognise',
                'C' => 'to emphasise that feeling lonely is more common for men than women',
            ]],
            ['The students agree that talking to strangers is a good strategy for dealing with loneliness because', 'A', [
                'A' => 'it creates a sense of belonging.',
                'B' => 'it builds self-confidence.',
                'C' => 'it makes people feel more positive.',
            ]],
            ['The students find it difficult to understand why solitude is considered to be', 'C', [
                'A' => 'similar to loneliness.',
                'B' => 'necessary for mental health.',
                'C' => 'an enjoyable experience.',
            ]],
        ];
        foreach ($s3McqData as $mcq) {
            $q = Question::updateOrCreate(
                ['questionable_type' => \App\Models\ListeningSection::class, 'questionable_id' => $sec3->id, 'question_text' => $mcq[0]],
                ['question_type' => 'multiple_choice', 'correct_answer' => $mcq[1]]
            );
            foreach ($mcq[2] as $letter => $text) {
                \App\Models\QuestionOption::updateOrCreate(
                    ['question_id' => $q->id, 'option_text' => $text],
                    ['is_correct' => ($letter === $mcq[1])]
                );
            }
        }

        // Section 4: Fill-in-blank Q31-40
        $s4Questions = [
            ['pollution from ______ on the river bank.', 'factories'],
            ['In 1957, the River Thames in London was declared biologically ______.', 'dead'],
            ['Seals and even a ______ have been seen in the River Thames.', 'whale'],
            ['Riverside warehouses are converted to restaurants and ______.', 'apartments'],
            ['build a riverside ______', 'park'],
            ['display ______ projects.', 'art'],
            ['In Paris, ______ are created on the sides of the river every summer.', 'beaches'],
            ['Over 2 billion passengers already travel by ______ in cities round the world.', 'ferry'],
            ['Instead of road transport, goods could be transported by large freight barges and electric ______', 'bikes'],
            ['or, in future, by ______.', 'drone'],
        ];
        foreach ($s4Questions as $sq) {
            Question::updateOrCreate(
                ['questionable_type' => \App\Models\ListeningSection::class, 'questionable_id' => $sec4->id, 'question_text' => $sq[0]],
                ['question_type' => 'short_answer', 'correct_answer' => $sq[1]]
            );
        }

        $this->command->info('✅ IELTS 20 (2025) Test 1 seeded: 3 reading (40Q) + 2 writing + 11 speaking + 4 listening sections (40Q).');
    }

    private function createGroup(ReadingPassage $passage, string $type, string $instruction, int $order): ReadingQuestionGroup
    {
        return ReadingQuestionGroup::updateOrCreate(
            ['reading_passage_id' => $passage->id, 'sort_order' => $order],
            ['question_type' => $type, 'group_instruction' => $instruction]
        );
    }

    private function seedQuestions(ReadingQuestionGroup $group, string $type, array $items): void
    {
        // Remove old questions for this group first
        $group->questions()->each(fn(Question $q) => tap($q, fn(Question $q) => $q->options()->delete())->delete());

        foreach ($items as [$text, $answer]) {
            $group->questions()->create([
                'question_type' => $type,
                'question_text' => $text,
                'correct_answer' => $answer,
            ]);
        }
    }

    private function seedMCQLetters(ReadingQuestionGroup $group, array $items): void
    {
        $group->questions()->each(fn(Question $q) => tap($q, fn(Question $q) => $q->options()->delete())->delete());

        foreach ($items as [$text, $answer]) {
            $group->questions()->create([
                'question_type' => 'multiple_choice',
                'question_text' => $text,
                'correct_answer' => $answer,
            ]);
        }
    }

    private function seedMCQWithOptions(ReadingQuestionGroup $group, array $items): void
    {
        $group->questions()->each(fn(Question $q) => tap($q, fn(Question $q) => $q->options()->delete())->delete());

        foreach ($items as [$text, $options, $correctLetter]) {
            $q = $group->questions()->create([
                'question_type' => 'multiple_choice',
                'question_text' => $text,
                'correct_answer' => $correctLetter,
            ]);

            foreach ($options as $i => $optText) {
                $letter = chr(65 + $i); // A, B, C, D
                $q->options()->create([
                    'option_text' => $optText,
                    'is_correct' => ($letter === $correctLetter),
                ]);
            }
        }
    }

    private function passage1Content(): string
    {
        return '<p>The kākāpō is a nocturnal, flightless parrot that is critically endangered and one of New Zealand\'s unique treasures.</p>

<p>The kākāpō, also known as the owl parrot, is a large, forest-dwelling bird, with a pale owl-like face. Up to 64 cm in length, it has predominantly yellow-green feathers, forward-facing eyes, a large grey beak, large blue feet, and relatively short wings and tail. It is the world\'s only flightless parrot, and is also possibly one of the world\'s longest-living birds, with a reported lifespan of up to 100 years.</p>

<p>Kākāpō are solitary birds and tend to occupy the same home range for many years. They forage on the ground and climb high into trees. They often leap from trees and flap their wings, but at best manage a controlled descent to the ground. They are entirely vegetarian, with their diet including the leaves, roots and bark of trees as well as bulbs, and fern fronds.</p>

<p>Kākāpō breed in summer and autumn, but only in years when food is plentiful. Males play no part in incubation or chick-rearing - females alone incubate eggs and feed the chicks. The 1-4 eggs are laid in soil, which is repeatedly turned over before and during incubation. The female kākāpō has to spend long periods away from the nest searching for food, which leaves the unattended eggs and chicks particularly vulnerable to predators.</p>

<p>Before humans arrived, kākāpō were common throughout New Zealand\'s forests. However, this all changed with the arrival of the first Polynesian settlers about 700 years ago. For the early settlers, the flightless kākāpō was easy prey. They ate its meat and used its feathers to make soft cloaks. With them came the Polynesian dog and rat, which also preyed on kākāpō. By the time European colonisers arrived in the early 1800s, kākāpō had become confined to the central North Island and forested parts of the South Island. The fall in kākāpō numbers was accelerated by European colonisation. A great deal of habitat was lost through forest clearance, and introduced species such as deer depleted the remaining forests of food. Other predators such as cats, stoats and two more species of rat were also introduced. The kākāpō were in serious trouble.</p>

<p>In 1894, the New Zealand government launched its first attempt to save the kākāpō. Conservationist Richard Henry led an effort to relocate several hundred of the birds to predator-free Resolution Island in Fiordland. Unfortunately, the island didn\'t remain predator free - stoats arrived within six years, eventually destroying the kākāpō population. By the mid-1900s, the kākāpō was practically a lost species. Only a few clung to life in the most isolated parts of New Zealand.</p>

<p>From 1949 to 1973, the newly formed New Zealand Wildlife Service made over 60 expeditions to find kākāpō, focusing mainly on Fiordland. Six were caught, but there were no females amongst them and all but one died within a few months of captivity. In 1974, a new initiative was launched, and by 1977, 18 more kākāpō were found in Fiordland. However, there were still no females. In 1977, a large population of males was spotted in Rakiura - a large island free from stoats, ferrets and weasels. There were about 200 individuals, and in 1980 it was confirmed females were also present. These birds have been the foundation of all subsequent work in managing the species.</p>

<p>Unfortunately, predation by feral cats on Rakiura Island led to a rapid decline in kākāpō numbers. As a result, during 1980-97, the surviving population was evacuated to three island sanctuaries: Codfish Island, Maud Island and Little Barrier Island. However, breeding success was hard to achieve. Rats were found to be a major predator of kākāpō chicks and an insufficient number of chicks survived to offset adult mortality. By 1995, although at least 12 chicks had been produced on the islands, only three had survived. The kākāpō population had dropped to 51 birds. The critical situation prompted an urgent review of kākāpō management in New Zealand.</p>

<p>In 1996, a new Recovery Plan was launched, together with a specialist advisory group called the Kākāpō Scientific and Technical Advisory Committee and a higher amount of funding. Renewed steps were taken to control predators on the three islands. Cats were eradicated from Little Barrier Island in 1980, and possums were eradicated from Codfish Island by 1986. However, the population did not start to increase until rats were removed from all three islands, and the birds were more intensively managed. This involved moving the birds between islands, supplementary feeding of adults and rescuing and hand-raising any failing chicks.</p>

<p>After the first five years of the Recovery Plan, the population was on target. By 2000, five new females had been produced, and the total population had grown to 62 birds. For the first time, there was cautious optimism for the future of kākāpō and by June 2020, a total of 210 birds was recorded.</p>

<p>Today, kākāpō management continues to be guided by the kākāpō Recovery Plan. Its key goals are: minimise the loss of genetic diversity in the kākāpō population, restore or maintain sufficient habitat to accommodate the expected increase in the kākāpō population, and ensure stakeholders continue to be fully engaged in the preservation of the species.</p>';
    }

    private function passage2Content(): string
    {
        return '<p><strong>Mark Rowe investigates attempts to reintroduce elms to Britain</strong></p>

<p><strong>A.</strong> Around 25 million elms, accounting for 90% of all elm trees in the UK, died during the 1960s and \'70s of Dutch elm disease. In the aftermath, the elm, once so dominant in the British landscape, was largely forgotten. However, there\'s now hope the elm may be reintroduced to the countryside of central and southern England. Any reintroduction will start from a very low base. "The impact of the disease is difficult to picture if you hadn\'t seen what was there before," says Matt Elliot of the Woodland Trust. "You look at old photographs from the 1960s and it\'s only then that you realise the impact [elms had]...They were significant, large trees...then they were gone."</p>

<p><strong>B.</strong> The disease is caused by a fungus that blocks the elms\' vascular (water, nutrient and food transport) system, causing branches to wilt and die. A first epidemic, which occurred in the 1920s, gradually died down, but in the \'70s a second epidemic was triggered by shipments of elm from Canada. The wood came in the form of logs destined for boat building and its intact bark was perfect for the elm bark beetles that spread the deadly fungus. This time, the beetles carried a much more virulent strain that destroyed the vast majority of British elms.</p>

<p><strong>C.</strong> Today, elms still exist in the southern English countryside but mostly only in low hedgerows between fields. "We have millions of small elms in hedgerows but they get targeted by the beetle as soon as they reach a certain size," says Karen Russell, co-author of the report \'Where we are with elm\'. Once the trunk of the elm reaches 10-15 centimetres or so in diameter, it becomes a perfect size for beetles to lay eggs and for the fungus to take hold. Yet mature specimens have been identified, in counties such as Cambridgeshire, that are hundreds of years old, and have mysteriously escaped the epidemic. The key, Russell says, is to identify and study those trees that have survived and work out why they stood tall when millions of others succumbed. Nevertheless, opportunities are limited as the number of these mature survivors is relatively small. "What are the reasons for their survival?" asks Russell. "Avoidance, tolerance, resistance? We don\'t know where the balance lies between the three. I don\'t see how it can be entirely down to luck."</p>

<p><strong>D.</strong> For centuries, elm ran a close second to oak as the hardwood tree of choice in Britain and was in many instances the most prominent tree in the landscape. Not only was elm common in European forests, it became a key component of birch, ash and hazel woodlands. The use of elm is thought to go back to the Bronze Age, when it was widely used for tools. Elm was also the preferred material for shields and early swords. In the 18th century, it was planted more widely and its wood was used for items such as storage crates and flooring. It was also suitable for items that experienced high levels of impact and was used to build the keel of the 19th-century sailing ship Cutty Sark as well as mining equipment.</p>

<p><strong>E.</strong> Given how ingrained elm is in British culture, it\'s unsurprising the tree has many advocates. Amongst them is Peter Bourne of the National Elm Collection in Brighton. "I saw Dutch elm disease unfold as a small boy," he says. "The elm seemed to be part of rural England, but I remember watching trees just lose their leaves and that really stayed with me." Today, the city of Brighton\'s elms total about 17,000. Local factors appear to have contributed to their survival. Strong winds from the sea make it difficult for the determined elm bark beetle to attack this coastal city\'s elm population. However, the situation is precarious. "The beetles can just march in if we\'re not careful, as the threat is right on our doorstep," says Bourne.</p>

<p><strong>F.</strong> Any prospect of the elm returning relies heavily on trees being either resistant to, or tolerant of, the disease. This means a widespread reintroduction would involve existing or new hybrid strains derived from resistant, generally non-native elm species. A new generation of seedlings have been bred and tested to see if they can withstand the fungus by cutting a small slit on the bark and injecting a tiny amount of the pathogen. "The effects are very quick," says Russell. "You return in four to six weeks and trees that are resistant show no symptoms, whereas those that are susceptible show leaf loss and may even have died completely."</p>

<p><strong>G.</strong> All of this raises questions of social acceptance, acknowledges Russell. "If we\'re putting elm back into the landscape, a small element of it is not native - are we bothered about that?" For her, the environmental case for reintroducing elm is strong. "They will host wildlife, which is a good thing." Others are more wary. "On the face of it, it seems like a good idea," says Elliot. The problem, he suggests, is that, "You\'re replacing a native species with a horticultural analogue. You\'re effectively cloning." There\'s also the risk of introducing new diseases. Rather than plant new elms, the Woodland Trust emphasises providing space to those elms that have survived independently. "Sometimes the best thing you can do is just give nature time to recover... over time, you might get resistance," says Elliot.</p>';
    }
    private function passage3Content(): string
    {
        return '<p><strong>The impact of stress and social media on mental health</strong></p>

<p>In recent years, the relationship between stress and social media has become a significant area of psychological research. Studies focusing on high-stress professions, such as firefighters, have provided valuable insights into how individuals respond to pressure. Researchers found that while firefighters are generally well-equipped to handle acute stress during emergencies, the chronic stress associated with their work can have long-term effects on their well-being.</p>

<p>One interesting finding is the "mechanism in the brain" that produces heightened sensitivity to external threats. This mechanism, while useful in dangerous situations, can become overactive in the context of social media. When individuals are constantly exposed to negative news or stressful posts in their feeds, their brains may remain in a state of high alert, leading to increased anxiety and a decreased ability to process information effectively.</p>

<p>Experiments involving students have also shown that cortisol levels and heart rates are significantly affected when they are placed in stressful situations or exposed to negative social media content. Interestingly, the tone of the content people post on social media tends to reflect the nature of the posts in their own feeds—a phenomenon known as emotional contagion. This suggests that our tendency to be affected by other people\'s moods can be used positively if we curate our social media environment to include more uplifting content.</p>

<p>Furthermore, stress created by social media posts can lead individuals to take unnecessary precautions or feel a sense of impending doom, even when the actual threat is minimal. The key to managing this stress lies in understanding these psychological mechanisms and taking proactive steps to limit exposure to triggers while fostering a more balanced digital life.</p>';
    }
}
