<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * The "iamPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $iamService = new Google_Service_Iam(...);
 *   $iamPolicies = $iamService->iamPolicies;
 *  </code>
 */
class Google_Service_Iam_Resource_IamPolicies extends Google_Service_Resource
{
  /**
   * Lints, or validates, an IAM policy. Currently checks the
   * google.iam.v1.Binding.condition field, which contains a condition expression
   * for a role binding.
   *
   * Successful calls to this method always return an HTTP `200 OK` status code,
   * even if the linter detects an issue in the IAM policy.
   * (iamPolicies.lintPolicy)
   *
   * @param Google_Service_Iam_LintPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_Iam_LintPolicyResponse
   */
  public function lintPolicy(Google_Service_Iam_LintPolicyRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('lintPolicy', array($params), "Google_Service_Iam_LintPolicyResponse");
  }
  /**
   * Returns a list of services that allow you to opt into audit logs that are not
   * generated by default.
   *
   * To learn more about audit logs, see the [Logging
   * documentation](https://cloud.google.com/logging/docs/audit).
   * (iamPolicies.queryAuditableServices)
   *
   * @param Google_Service_Iam_QueryAuditableServicesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Google_Service_Iam_QueryAuditableServicesResponse
   */
  public function queryAuditableServices(Google_Service_Iam_QueryAuditableServicesRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('queryAuditableServices', array($params), "Google_Service_Iam_QueryAuditableServicesResponse");
  }
}
