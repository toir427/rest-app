<?php

namespace console\controllers;

use common\models\User;
use common\rbac\UpdateCompanyRule;
use common\rbac\UpdateEmployeeRule;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $users = [
            [
                'username' => 'admin',
                'password' => 'random1',
                'email' => 'admin@gmail.com',
                'roles' => [
                    User::ROLE_ADMIN => [
                        // can see companies list and manage them (CRUD)
                        User::PERMISSION_COMPANIES_LIST,
                        User::PERMISSION_COMPANY_DETAIL,
                        User::PERMISSION_CREATE_COMPANY,
                        User::PERMISSION_UPDATE_COMPANY,
                        User::PERMISSION_DELETE_COMPANY,
                        // can see employees list
                        User::PERMISSION_EMPLOYEES_LIST,
                    ]
                ]
            ],
            [
                'username' => 'company',
                'password' => 'random1',
                'email' => 'company@gmail.com',
                'roles' => [
                    User::ROLE_COMPANY => [
                        // company can change its information
                        User::PERMISSION_COMPANIES_LIST,
                        User::PERMISSION_COMPANY_DETAIL,
                        User::PERMISSION_CREATE_COMPANY,
                        User::PERMISSION_UPDATE_COMPANY,
                        //User::PERMISSION_DELETE_COMPANY,

                        // company can change information belongs to employees
                        User::PERMISSION_EMPLOYEES_LIST,
                        User::PERMISSION_EMPLOYEE_DETAIL,
                        User::PERMISSION_CREATE_EMPLOYEE,
                        User::PERMISSION_UPDATE_EMPLOYEE,
                        User::PERMISSION_DELETE_EMPLOYEE,
                    ]
                ]
            ]
        ];

        foreach (User::$permissions as $permission) {
            $uPermission = $auth->createPermission($permission);
            $uPermission->description = $permission;
            $auth->add($uPermission);
        }

        foreach ($users as $user) {
            $new = new User();
            $new->username = $user['username'];
            $new->email = $user['email'];
            $new->setPassword($user['password']);
            $new->generateAuthKey();
            if ($new->save()) {
                foreach ($user['roles'] as $role => $permissions) {
                    $uRole = $auth->createRole($role);
                    $auth->add($uRole);

                    if ($role === User::ROLE_ADMIN) {
                        $rule = new UpdateCompanyRule();
                        $auth->add($rule);

                        $updateOwnCompany = $auth->createPermission(User::PERMISSION_UPDATE_OWN_COMPANY);
                        $updateOwnCompany->ruleName = $rule->name;
                        $auth->add($updateOwnCompany);
                        $auth->addChild($uRole, $updateOwnCompany);
                    }

                    if ($role === User::ROLE_COMPANY) {
                        $rule = new UpdateEmployeeRule();
                        $auth->add($rule);

                        $updateOwnEmployee = $auth->createPermission(User::PERMISSION_UPDATE_OWN_EMPLOYEE);
                        $updateOwnEmployee->ruleName = $rule->name;
                        $auth->add($updateOwnEmployee);
                        $auth->addChild($uRole, $updateOwnEmployee);
                    }

                    $auth->assign($uRole, $new->id);

                    foreach ($permissions as $permission) {
                        $pName = $auth->getPermission($permission);
                        $auth->addChild($uRole, $pName);
                    }
                }
            }
        }
    }
}