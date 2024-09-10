<?php

namespace App\Security\Voter;

use App\Entity\Survey;
use App\Entity\User;
use App\Enums\SurveyStatusEnum;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ContragentCredentialVoter extends Voter
{
    public const SHOW_CREDENTIAL = 'SURVEY_AGREEMENT_DOWNLOAD';

    /**
     * @param Security $security
     */
    public function __construct(
        protected Security $security
    ) {
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::AGREEMENT_DOWNLOAD])
            && $subject instanceof Survey;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::AGREEMENT_DOWNLOAD:
                return $this->canAgreementDownload($subject, $user);
        }

        return false;
    }

    /**
     * @param Survey $survey
     * @param User $user
     * @return bool
     */
    private function canAgreementDownload(Survey $survey, User $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($survey->getStatusState() !== SurveyStatusEnum::STATUS_FINISH->value) {
            return false;
        }

        if ($survey->getUser()->getId() !== $user->getId()) {
            return false;
        }

        return true;
    }
}
