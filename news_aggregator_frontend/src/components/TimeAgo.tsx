
type TimeAgoProps = {
    timestamp: string
}
function TimeAgo({ timestamp }: TimeAgoProps) {
  const timeAgo = () => {
    const currentTime = new Date();
    const pastTime = new Date(timestamp);
    const timeDifference = currentTime.getTime() - pastTime.getTime();

    const minutes = Math.floor(timeDifference / (1000 * 60));
    const hours = Math.floor(timeDifference / (1000 * 60 * 60));
    const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

    if (minutes < 60) {
      return `${minutes} minutes ago`;
    } else if (hours < 24) {
      return `${hours} hours ago`;
    } else {
      return `${days} days ago`;
    }
  };

  return <span>{timeAgo()}</span>;
}

export default TimeAgo;
