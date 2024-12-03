<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $results = [
            'board_id' => ['required', 'integer', 'exists:boards,id'],
            'category_id' => ['nullable', 'integer'/*, 'required_if:board_id,' . Board::POINT_BOARD_ID*/],
            'title' => ['required'],
            'content' => ['required'],
            'files' => ['array', 'nullable'],
            //'files.*.name' => 'required',
            'created_by' => ['nullable'],
            //'is_notice' => ['nullable', 'boolean'],
            //'is_popup' => ['nullable', 'boolean'],
            'start_date' => ['nullable'],
            'end_date' => ['nullable'],
        ];

        if (request()->isMethod('post')) {
        }

        if (request()->isMethod('put')) {
            $results['id'] = ['required'];
            $results['content_answer'] = ['nullable'];
            $results['answered_by'] = ['nullable'];
            $results['answered_at'] = ['nullable'];
        }

        return $results;
    }

    public function prepareForValidation()
    {
        $inputs = $this->input();
        foreach ($inputs as $key => $input) {
            if ($input === 'null') $inputs[$key] = null;
            if ($input === 'true') $inputs[$key] = true;
            if ($input === 'false') $inputs[$key] = false;
        }
        if (request()->isMethod('post')) {
            $inputs['created_by'] = auth()->user()->id;
        }
        if (request()->isMethod('put')) {
            $inputs['updated_by'] = auth()->user()->id;
            if ($inputs['content_answer']) {
                $inputs['answered_by'] = auth()->user()->id;
                $inputs['answered_at'] = now();
            }
        }
        $this->merge($inputs);
    }

    public function messages()
    {
        return [
            'category_id.required_if' => 'category id 필드는 필수입니다.',
        ];
    }

    public function bodyParameters()
    {
        return [
            'id' => ['description' => '<span class="point">기본키</span>'],
            'board_id' => ['description' => '<span class="point">board 기본키</span>'],
            'category_id' => ['description' => '<span class="point">카테고리 기본키</span>'],
            'title' => ['description' => '<span class="point">제목</span>'],
            'content' => ['description' => '<span class="point">내용</span>'],
            'content_answer' => ['description' => '<span class="point">답변내용</span>'],
            'is_notice' => ['description' => '<span class="point">공지사항 여부</span>'],
            'is_notice_top' => ['description' => '<span class="point">상단 공지사항 여부</span>'],
            'is_html' => ['description' => '<span class="point">내용 html 여부</span>'],
            'is_secret' => ['description' => '<span class="point">비밀글 여부</span>'],
            'is_popup' => ['description' => '<span class="point">팝업 여부</span>'],
            'start_date' => ['description' => '<span class="point">시작일</span>'],
            'end_date' => ['description' => '<span class="point">종료일</span>'],
            'read_count' => ['description' => '<span class="point">조회수</span>'],
            'comment_count' => ['description' => '<span class="point">답글숫</span>'],
            'like_count' => ['description' => '<span class="point">좋아요 개수</span>'],
            'dislike_count' => ['description' => '<span class="point">싫어요 개수</span>'],
            'created_by' => ['description' => '<span class="point">작성자 아이디</span>'],
            'updated_by' => ['description' => '<span class="point">수정자 아이디</span>'],
            'deleted_by' => ['description' => '<span class="point">삭제자 아이디</span>'],
            'answered_at' => ['description' => '<span class="point">답변일시</span>'],
            'answered_by' => ['description' => '<span class="point">답변자 아이디</span>'],
            'created_at' => ['description' => '<span class="point"></span>'],
            'updated_at' => ['description' => '<span class="point"></span>'],
            'deleted_at' => ['description' => '<span class="point"></span>'],
        ];
    }

}
