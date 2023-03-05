import React, { useEffect } from 'react'
import { useSendConfirmation } from '../api/apiConfirmMail';

const WaitingForRegistration = () => {

  const handleSendConfirmation = useSendConfirmation()

  useEffect(() => {
    handleSendConfirmation.mutate()
  }, [])

  if (handleSendConfirmation.isLoading) {
    return (
      <div>
        Loading...
      </div>
    )
  }



  return (
    <div>
      Please check you're email to confirm your registration
      {/* button to reaload page if email is not send */}
      <br />
      <button onClick={() => handleSendConfirmation.mutate()}>Send email again</button>
    </div>
  )
}

export default WaitingForRegistration
