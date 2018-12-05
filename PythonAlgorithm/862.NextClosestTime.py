'''
Given a time represented in the format "HH:MM", form the next closest time by reusing the current digits. There is no limit on how many times a digit can be reused.

You may assume the given input string is always valid. For example, "01:34", "12:09" are all valid. "1:34", "12:9" are all invalid.

Example
Given time = "19:34", return "19:39".

Explanation: 
The next closest time choosing from digits 1, 9, 3, 4, is 19:39, which occurs 5 minutes later.  It is not 19:33, because this occurs 23 hours and 59 minutes later.
Given time = "23:59", return "22:22".'''
class Solution():
    def nextClosestTime(self, timestr):
        if timestr is None or len(timestr) == 0:
            return timestr

        nums = []
        for i in range(len(timestr)):
            if timaestr[i] != ":":
                nums.append(timestr[i])

        if len(set(nums)) == 1:
            return timestr

        import sys
        self.diff = sys.maxsize
        self.result = []
        minute = int(timestr[0:2]) * 60 + int(timestr[3:5])
        self.search(nums, "", 0, minute)
        #print(self.result)
        resulttime = self.result[0:2] + ":" + self.result[2:4]
        return resulttime

    def search(self, nums, currtime, index, target):
        print("curr time")
        print(currtime)
        print("index")
        print(index)
        if index == 4:
            m = int(currtime[0:2]) * 60 + int(currtime[2:4])
            if m == target:
                print("target target target target target")
                return
            if m - target > 0:
                d = m - target
            else:
                d = 1440 + m - target # 24hour * 60min = 1440 min
            if d < self.diff:
                self.diff = d
                self.result = currtime
                print("the result time is")
                print(self.result)
            return

        for i in range(0, len(nums)):
            print("i is")
            print(i)

            if index == 0 and int(nums[i]) > 2:
                continue
            if index == 1 and int(currtime) * 10 + int(nums[i]) > 23:
                continue
            if index == 2 and int(nums[i]) > 5:
                continue
            if index == 3 and int(currtime[2]) * 10 + int(nums[i]) > 59:
                continue
            self.search(nums, currtime + nums[i], index+1, target)

x = Solution( )
print(x.nextClosestTime("19:34"))