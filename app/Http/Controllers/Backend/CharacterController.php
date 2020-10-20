<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\CV;
use App\Models\Guild;
use App\Models\Nickname;
use App\Models\Race;
use App\Models\Skill;
use App\Models\SkillType;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CharacterController extends Controller
{
    /**
     * 回應
     *
     * @var \App\Services\ResponseService
     */
    protected $response;

    /**
     * 建構函式
     *
     * @return void
     */
    public function __construct(ResponseService $response)
    {
        $this->response = $response;
    }

    /**
     * 新增角色資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @return \Illuminate\Http\JsonResponse 200 回應或錯誤訊息
     */
    public function addCharacter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tw_name' => ['required', 'string', 'max:10'],
            'jp_name' => ['required', 'string', 'max:15'],
            'cv_of' => ['required', 'numeric'],
            'race_of' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max: 255'],
            'ages' => ['required', 'numeric', 'min:0'],
            'height' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'nicknames' => ['required', 'array', 'min:1'],
            'nicknames.*' => ['required', 'string'],
            'likes' => ['required', 'array', 'min:1'],
            'likes.*' => ['required', 'string'],
            'birthday' => ['required', 'date'],
            'guild_of' => ['required', 'numeric'],
            'blood_type' => ['nullable', 'string', 'in:A,B,O,AB'],
            's_image_url' => ['nullable', 'string'],
            'f_image_url' => ['nullable', 'string'],
            't_image_url' => ['nullable', 'string'],
            'skills' => ['required', 'array'],
            'skills.*.skill_type_of' => ['required', 'numeric'],
            'skills.*.skill_name' => ['nullable', 'string', 'max:15'],
            'skills.*.description' => ['nullable', 'string', 'max:255'],
            'skills.*.effect' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->response
                        ->setError('請確認是否仍有資料未填或有資料格式不正確')
                        ->setCode($this->response::BAD_REQUEST)
                        ->json();
        }

        $request->merge(['likes' => json_encode($request->input('likes'), JSON_UNESCAPED_UNICODE)]);
        $character = $request->except(['nicknames', 'skills']);
        $nicknames = $request->input('nicknames');
        $skills = $request->input('skills');

        $id = Character::create($character);
        $created_at = $id->created_at;
        $updated_at = $id->updated_at;
        $id = $id->id;

        $nicknames = collect($nicknames)->map(function ($item) use ($id, $created_at, $updated_at) {
            $newItem = [
                'nickname' => $item,
                'character_of' => $id,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
            ];
            return $newItem;
        })->toArray();

        Nickname::insert($nicknames);

        $skills = collect($skills)->map(function ($item) use ($id, $created_at, $updated_at) {
            if (empty($item['description']) || empty($skill_name)) {
                return null;
            }
            $item['character_of'] = $id;
            $item['created_at'] = $created_at;
            $item['updated_at'] = $updated_at;
            return $item;
        })->filter()->toArray();

        Skill::insert($skills);

        return $this->response->setData($id)->json();
    }

    /**
     * 取得公會清單
     *
     * @return \Illuminate\Http\JsonResponse 所有公會清單
     */
    public function guildList()
    {
        $guilds = Guild::select('id', 'name', 'deleted_at')->withTrashed()->get();

        return $this->response->setData($guilds)->json();
    }

    /**
     * 取得所有技能種類
     *
     * @return \Illuminate\Http\JsonResponse 所有公會清單
     */
    public function skillTypeList()
    {
        $skillTypes = SkillType::select('id', 'name', 'deleted_at')->withTrashed()->get();

        return $this->response->setData($skillTypes)->json();
    }

    /**
     * 取得所有聲優清單
     *
     * @return \Illuminate\Http\JsonResponse 所有聲優清單
     */
    public function CVList()
    {
        $cvs = CV::select('id', 'name', 'deleted_at')->withTrashed()->get();

        return $this->response->setData($cvs)->json();
    }

    /**
     * 取得所有種族清單
     *
     * @return \Illuminate\Http\JsonResponse 所有種族清單
     */
    public function raceList()
    {
        $races = Race::select('id', 'name', 'deleted_at')->withTrashed()->get();

        return $this->response->setData($races)->json();
    }

    /**
     * 新增聲優、公會、種族、技能種類資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @param string|null $data [cv|guild|race|skilltype]
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRelatedData(Request $request, string $data = null)
    {
        $validator = Validator::make(['data' => $data], [
            'data' => ['required', 'string', 'in:cv,guild,race,skilltype'],
        ]);

        if ($validator->fails()) {
            return $this->response->setCode($this->response::NOT_FOUND)->json();
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
        ]);

        switch ($data) {
            case 'cv':
                // 新增聲優資料
                if ($validator->fails()) {
                    return $this->response
                                ->setError('聲優的名稱未填或格式不正確')
                                ->setCode($this->response::BAD_REQUEST)
                                ->json();
                }

                $cv = CV::where('name', $request->input('name'))->count();

                if ($cv > 0) {
                    return $this->response
                                ->setError('該聲優已經存在！')
                                ->setCode($this->response::BAD_REQUEST)
                                ->json();
                }

                $id = CV::create([
                    'name' => $request->input('name'),
                ]);
                break;
            case 'guild':
                // 新增公會資料
                if ($validator->fails()) {
                    return $this->response
                                ->setError('公會的名稱未填或格式不正確')
                                ->setCode($this->response::BAD_REQUEST)
                                ->json();
                }

                $guild = Guild::where('name', $request->input('name'))->count();

                if ($guild > 0) {
                    return $this->response
                                ->setError('該公會已經存在！')
                                ->setCode($this->response::BAD_REQUEST)
                                ->json();
                }

                $id = Guild::create([
                    'name' => $request->input('name'),
                ]);
                break;
            case 'race':
                // 新增種族資料
                if ($validator->fails()) {
                    return $this->response
                                ->setError('種族的名稱未填或格式不正確')
                                ->setCode($this->response::BAD_REQUEST)
                                ->json();
                }

                $guild = Race::where('name', $request->input('name'))->count();

                if ($guild > 0) {
                    return $this->response
                                ->setError('該種族已經存在！')
                                ->setCode($this->response::BAD_REQUEST)
                                ->json();
                }

                $id = Race::create([
                    'name' => $request->input('name'),
                ]);
                break;
            case 'skilltype':
                // 新增技能種類資料
                if ($validator->fails()) {
                    return $this->response
                                ->setError('技能的種類名稱未填或格式不正確')
                                ->setCode($this->response::BAD_REQUEST)
                                ->json();
                }

                $guild = SkillType::where('name', $request->input('name'))->count();

                if ($guild > 0) {
                    return $this->response
                                ->setError('該技能種類已經存在！')
                                ->setCode($this->response::BAD_REQUEST)
                                ->json();
                }

                $id = SkillType::create([
                    'name' => $request->input('name'),
                ]);
                break;
        }

        return $this->response->setData($id->id)->json();
    }

    /**
     * 編輯聲優、公會、種族、技能種類資料
     *
     * @param \Illuminate\Http\Request $request HTTP 請求
     * @param string|null $data [cv|guild|race|skilltype]
     */
    public function editRelatedData(Request $request, string $data = null)
    {
        $request->merge(['data' => $data]);
        $validator = Validator::make($request->all(), [
            'data' => ['required', 'string', 'in:cv,guild,race,skilltype'],
            'id' => ['required', 'numeric'],
            'name' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->response->setCode($this->response::NOT_FOUND)->json();
        }

        switch ($data) {
            case 'cv':
                CV::where('id', $request->input('id'))->update([
                    'name' => $request->input('name'),
                ]);
                break;
            case 'guild':
                Guild::where('id', $request->input('id'))->update([
                    'name' => $request->input('name'),
                ]);
                break;
            case 'race':
                Race::where('id', $request->input('id'))->update([
                    'name' => $request->input('name'),
                ]);
                break;
            case 'skilltype':
                SkillType::where('id', $request->input('id'))->update([
                    'name' => $request->input('name'),
                ]);
                break;
        }

        return $this->response->json();
    }
}
