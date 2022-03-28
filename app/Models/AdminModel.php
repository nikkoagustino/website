<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class AdminModel extends Model
{
    use HasFactory;

    static function addSystemLog($action) {
        $insert = DB::table('tb_system_log')
                    ->insert([
                        'username' => Session::get('username'),
                        'action' => $action,
                    ]);
        return $insert;
    }

    static function getSystemLog() {
        $result = DB::table('tb_system_log')
                    ->orderBy('id', 'desc')
                    ->paginate(100);
        return $result;
    }

    static function getUserList() {
        $result = DB::table('tb_useradmin')
                    ->get();
        return $result;
    }

    static function getUserAdmin($username) {
        $result = DB::table('tb_useradmin')
                    ->where('username', '=', $username)
                    ->first();
        return $result;
    }

    static function getFiveNews($lang) {
        $result = DB::table('tb_news_'.$lang)
                    ->orderBy('is_pinned', 'desc')
                    ->orderBy('right_pinned', 'desc')
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get();
        return $result;
    }

    static function getNews($lang) {
        $result = DB::table('tb_news_'.$lang)
                    ->orderBy('is_pinned', 'desc')
                    ->orderBy('right_pinned', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(20);
        return $result;
    }

    static function submitNews($req, $featured_image) {
        $left_pinned = 0;
        $right_pinned = 0;
        if ($req->pinned_post == 'left') {
            self::unpinNews($req->language);
            $left_pinned = 1;
        } else if ($req->pinned_post == 'right') {
            self::unpinRightNews($req->language);
            $right_pinned = 1;
        }

        $insert = DB::table('tb_news_'.$req->language)
                    ->insert([
                        'slug' => $req->slug,
                        'title' => $req->title,
                        'featured_image' => $featured_image,
                        'news_content' => $req->news_content,
                        'posted_by' => Session::get('fullname'),
                        'is_pinned' => $left_pinned,
                        'right_pinned' => $right_pinned,
                        'visibility' => $req->visibility,
                    ]);
        return $insert;
    }

    static function getSingleNews($lang, $id) {
        $result = DB::table('tb_news_'.$lang)
                    ->where('id', '=', $id)
                    ->first();
        return $result;
    }

    static function deleteNews($lang, $id) {
        $delete = DB::table('tb_news_'.$lang)
                    ->where('id', '=', $id)
                    ->delete();
        return $delete;
    }

    static function unpinNews($lang) {
        $update = DB::table('tb_news_'.$lang)
                        ->update([
                            'is_pinned' => "0",
                        ]);
        return $update;
    }

    static function pinNews($lang, $id) {
        self::unpinNews($lang);
        $update = DB::table('tb_news_'.$lang)
                        ->where('id', '=', $id)
                        ->update([
                            'is_pinned' => "1",
                        ]);
        return $update;
    }

    static function unpinRightNews($lang) {
        $update = DB::table('tb_news_'.$lang)
                        ->update([
                            'right_pinned' => "0",
                        ]);
        return $update;
    }

    static function pinRightNews($lang, $id) {
        self::unpinRightNews($lang);
        $update = DB::table('tb_news_'.$lang)
                        ->where('id', '=', $id)
                        ->update([
                            'right_pinned' => "1",
                        ]);
        return $update;
    }

    static function getNewsletterRecipient() {
        $result = DB::table('tb_newsletter_subscriber')
                    ->paginate(100);
        return $result;
    }

    static function changePassword($req, $userdata) {
        $update = DB::table('tb_useradmin')
                    ->where('username', '=', $userdata->username)
                    ->update([
                        'password' => md5($req->new_password.$userdata->salt),
                    ]);
        return $update;
    }

    static function submitUserAdmin($req) {
        $salt_gen = md5(rand().DATE_ATOM);
        $insert = DB::table('tb_useradmin')
                    ->insert([
                        'username' => $req->username,
                        'fullname' => $req->fullname,
                        'salt' => $salt_gen,
                        'password' => md5($req->password.$salt_gen),
                        'access_level' => $req->access_level,
                    ]);
        return $insert;
    }

    static function deleteUserAdmin($username) {
        $delete = DB::table('tb_useradmin')
                    ->where('username', '=', $username)
                    ->delete();
        return $delete;
    }


    static function getFivePartners($lang) {
        $result = DB::table('tb_partners_'.$lang)
                    ->orderBy('is_pinned', 'desc')
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get();
        return $result;
    }

    static function getPartners($lang) {
        $result = DB::table('tb_partners_'.$lang)
                    ->orderBy('is_pinned', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(20);
        return $result;
    }

    static function submitPartner($req, $featured_image) {
        if ($req->is_pinned == 1) {
            self::unpinPartner($req->language);
        }

        $insert = DB::table('tb_partners_'.$req->language)
                    ->insert([
                        'slug' => $req->slug,
                        'title' => $req->title,
                        'featured_image' => $featured_image,
                        'news_content' => $req->news_content,
                        'posted_by' => Session::get('fullname'),
                        'is_pinned' => $req->is_pinned,
                        'visibility' => $req->visibility,
                    ]);
        return $insert;
    }

    static function getSinglePartner($lang, $id) {
        $result = DB::table('tb_partners_'.$lang)
                    ->where('id', '=', $id)
                    ->first();
        return $result;
    }

    static function deletePartner($lang, $id) {
        $delete = DB::table('tb_partners_'.$lang)
                    ->where('id', '=', $id)
                    ->delete();
        return $delete;
    }

    static function unpinPartner($lang) {
        $update = DB::table('tb_partners_'.$lang)
                        ->update([
                            'is_pinned' => "0",
                        ]);
        return $update;
    }

    static function pinPartner($lang, $id) {
        self::unpinPartner($lang);
        $update = DB::table('tb_partners_'.$lang)
                        ->where('id', '=', $id)
                        ->update([
                            'is_pinned' => "1",
                        ]);
        return $update;
    }

    static function getMedia() {
        $result = DB::table('tb_media_upload')
                    ->orderBy('uploaded', 'desc')
                    ->paginate(100);
        return $result;
    }

    static function addMediaDatabase($filename) {
        $insert = DB::table('tb_media_upload')
                    ->insert([
                        'filename' => $filename,
                        'upload_by' => Session::get('username'),
                    ]);
        return $insert;
    }

    static function getFiveAcademy($lang) {
        $result = DB::table('tb_academy_'.$lang)
                    ->orderBy('is_pinned', 'desc')
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get();
        return $result;
    }

    static function getAcademy($lang) {
        $result = DB::table('tb_academy_'.$lang)
                    ->orderBy('is_pinned', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(20);
        return $result;
    }

    static function submitAcademy($req, $featured_image) {
        if ($req->is_pinned == 1) {
            self::unpinAcademy($req->language);
        }

        $insert = DB::table('tb_academy_'.$req->language)
                    ->insert([
                        'slug' => $req->slug,
                        'title' => $req->title,
                        'featured_image' => $featured_image,
                        'news_content' => $req->news_content,
                        'posted_by' => Session::get('fullname'),
                        'is_pinned' => $req->is_pinned,
                        'visibility' => $req->visibility,
                        'level' => $req->level,
                    ]);
        return $insert;
    }

    static function getSingleAcademy($lang, $id) {
        $result = DB::table('tb_academy_'.$lang)
                    ->where('id', '=', $id)
                    ->first();
        return $result;
    }

    static function deleteAcademy($lang, $id) {
        $delete = DB::table('tb_academy_'.$lang)
                    ->where('id', '=', $id)
                    ->delete();
        return $delete;
    }

    static function unpinAcademy($lang) {
        $update = DB::table('tb_academy_'.$lang)
                        ->update([
                            'is_pinned' => "0",
                        ]);
        return $update;
    }

    static function pinAcademy($lang, $id) {
        self::unpinAcademy($lang);
        $update = DB::table('tb_academy_'.$lang)
                        ->where('id', '=', $id)
                        ->update([
                            'is_pinned' => "1",
                        ]);
        return $update;
    }
}